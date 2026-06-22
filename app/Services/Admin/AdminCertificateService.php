<?php

namespace App\Services\Admin;

use App\Http\Traits\HelperTrait;
use App\Models\CertificateTemplate;
use App\Models\UserCertificate;
use App\Repositories\Contracts\UserCertificateRepositoryInterface;
use App\Services\CertificateService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service backing the 2026 admin Certificates redesign.
 *
 * Strictly additive — the existing public certificates endpoints
 * (`/api/v1/certificates`) and the legacy admin blade certificate
 * controller continue to work untouched. This service adds two
 * concerns the legacy code couldn't express:
 *
 *  1) A persisted *template* concept (table: `certificate_templates`)
 *     that the admin can upload / replace / preview.
 *  2) A normalized **issued** list that the new Figma table consumes
 *     (employee_id / learner / course / issued_at + download anchor),
 *     unified across the two underlying sources of issuance
 *     (UserExam + UserCourseEvaluation).
 */
class AdminCertificateService
{
    use HelperTrait;

    public function __construct(
        private readonly UserCertificateRepositoryInterface $certificates,
        private readonly CertificateService $certificateService,
    ) {}

    /* ------------------------------------------------------------------ *
     |  Template (single active row)                                      |
     * ------------------------------------------------------------------ */

    /** Returns the currently active template row, or null. */
    public function activeTemplate(): ?CertificateTemplate
    {
        return CertificateTemplate::query()->active()->latest('updated_at')->first();
    }

    /**
     * Overview payload powering the Figma "NAS Standard Certificate"
     * card — template metadata + aggregate KPIs.
     *
     * @return array<string,mixed>
     */
    public function overview(): array
    {
        $template = $this->activeTemplate();

        [$totalIssued, $lastIssuedAt] = $this->issuedAggregates();

        return [
            'template' => $template ? [
                'id'                => (int) $template->id,
                'name'              => $template->name,
                'name_ar'           => $template->name_ar,
                'description'       => $template->description,
                'description_ar'    => $template->description_ar,
                // NOTE: we intentionally don't expose a public file URL.
                // Frontend always streams the file through the authenticated
                // `/admin/certificates/template/file` endpoint — that avoids
                // depending on the `storage:link` symlink, which is unreliable
                // under `php artisan serve` on Windows.
                'original_filename' => $template->original_filename,
                'mime_type'         => $template->mime_type,
                'file_size'         => $template->file_size,
                'has_file'          => !empty($template->file_path),
                'uploaded_at'       => optional($template->updated_at)->format('Y-m-d H:i:s'),
                'uploaded_by'       => $template->uploader?->name,
            ] : null,
            'stats' => [
                'total_issued'   => $totalIssued,
                'last_issued_at' => $lastIssuedAt,
            ],
            // Auto-filled field list is persisted per template in the
            // `certificate_templates.auto_fields` JSON column, so the
            // admin UI never shows hardcoded values.
            'fields' => $this->resolveAutoFields($template),
        ];
    }

    /**
     * @return array<int,string>
     */
    private function resolveAutoFields(?CertificateTemplate $template): array
    {
        $raw = $template?->auto_fields;

        if (is_array($raw)) {
            $clean = array_values(array_filter(
                array_map(static fn ($v) => is_string($v) ? trim($v) : '', $raw),
                static fn (string $v) => $v !== '',
            ));

            if (!empty($clean)) {
                return $clean;
            }
        }

        // Fallback if the column was somehow not populated (e.g. legacy
        // row inserted before the migration ran). The migration already
        // back-fills these — this branch is purely defensive.
        return $this->defaultAutoFields();
    }

    /**
     * @return array<int,string>
     */
    private function defaultAutoFields(): array
    {
        return [
            'Learner full name',
            'Course name',
            'Completion date',
            'Instructor name',
            'Certificate ID',
        ];
    }

    /**
     * Save the uploaded template, deactivate previously active rows.
     *
     * @return array<string,mixed>  Returns the updated overview payload.
     */
    public function uploadTemplate(UploadedFile $file, ?Authenticatable $admin): array
    {
        $storedPath = $this->storeTemplateFile($file);

        DB::transaction(function () use ($storedPath, $file, $admin) {
            // Carry the auto-fields list forward from the currently-active
            // row so admins keep whatever they had configured (or the
            // seeded defaults on a fresh install). Captured BEFORE we
            // deactivate, so the lookup still hits the active row.
            $autoFields = $this->resolveAutoFields($this->activeTemplate());

            // Deactivate any previously active template(s).
            CertificateTemplate::query()
                ->active()
                ->update(['is_active' => false, 'updated_at' => Carbon::now()]);

            CertificateTemplate::query()->create([
                'name'              => 'NAS Standard Certificate',
                'name_ar'           => 'شهادة NAS القياسية',
                'description'       => 'Default template — Auto-fills learner name, course, date, instructor',
                'description_ar'    => 'القالب الافتراضي — يملأ تلقائيًا اسم المتدرب والدورة والتاريخ والمدرب',
                'auto_fields'       => $autoFields,
                'file_path'         => $storedPath,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type'         => $file->getMimeType(),
                'file_size'         => $file->getSize(),
                'uploaded_by'       => $admin?->getAuthIdentifier(),
                'is_active'         => true,
            ]);

            // Mirror the new file into the legacy `settings.certificate`
            // row so the existing MVC /admin/settings page, the home-page
            // certificate section, and any other consumer reading
            // `$settings['certificate']` all see the same template.
            // Strictly additive — no MVC controller is touched.
            $this->syncLegacyCertificateSetting($storedPath);
        });

        // Invalidate the cached settings map used by the view composer in
        // AppServiceProvider (10-minute TTL) so the front layouts pick up
        // the new certificate immediately.
        Cache::forget('cms.settings.map');

        return $this->overview();
    }

    /**
     * Mirror the uploaded template path into the legacy `settings` table
     * row with key=`certificate` (type=`file`).
     *
     * Inserts a placeholder row if the seeded one is missing, so a fresh
     * install also stays in sync. The MVC's `SettingController` reads
     * `value` directly via `Storage::disk('public')->url(...)`, so any
     * relative path on the public disk works — we don't have to move the
     * file into `Setting/`.
     */
    private function syncLegacyCertificateSetting(string $storedPath): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $now = Carbon::now();
        $row = DB::table('settings')->where('key', 'certificate')->first();

        if ($row) {
            DB::table('settings')
                ->where('id', $row->id)
                ->update([
                    'value'      => $storedPath,
                    'type'       => $row->type ?: 'file',
                    'updated_at' => $now,
                ]);
            return;
        }

        DB::table('settings')->insert([
            'key'        => 'certificate',
            'label'      => 'الشهادة',
            'type'       => 'file',
            'value'      => $storedPath,
            'module'     => 'home',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /* ------------------------------------------------------------------ *
     |  Issued list (unified exam + evaluation certificates)              |
     * ------------------------------------------------------------------ */

    /**
     * Returns paginated issued-certificate rows in the shape the Figma
     * table expects.
     *
     * Filters supported: `search` (matches learner name, machine_code,
     * course title) and `course_id`.
     */
    public function paginateIssued(int $perPage, ?string $search, ?int $courseId): LengthAwarePaginator
    {
        $paginator = $this->certificates->paginateAdmin($perPage, $search, $courseId);
        $paginator->getCollection()->transform(fn (UserCertificate $c) => $this->formatRow($c));

        return $paginator;
    }

    /**
     * Render an issued certificate as a JPEG by certificate id and stream
     * it to the caller. Re-uses the legacy
     * `HelperTrait::generateCertificate()` so the visual output is
     * identical to the existing blade page.
     *
     * @return array{filename:string,binary:string}
     */
    public function renderById(int $certificateId): array
    {
        $certificate = $this->certificates->findById($certificateId);
        abort_if($certificate === null, 404);

        return $this->renderCertificate($certificate);
    }

    /**
     * Backward-compatible render by learner + course (used by the legacy
     * `/admin/certificates/{userId}/{courseId}/download` route the Angular
     * dashboard currently calls). Resolves the active certificate row.
     *
     * @return array{filename:string,binary:string}
     */
    public function renderIssuedCertificate(int $userId, int $courseId): array
    {
        $certificate = $this->certificates->findActiveByUserAndCourse($userId, $courseId);
        abort_if($certificate === null, 404);

        return $this->renderCertificate($certificate);
    }

    /**
     * Revoke an issued certificate (keeps the row + history).
     *
     * @return array<string,mixed>
     */
    public function revoke(int $certificateId, ?Authenticatable $admin): array
    {
        $certificate = $this->certificates->findById($certificateId);
        abort_if($certificate === null, 404);

        $certificate = $this->certificateService->revoke(
            $certificate,
            $admin?->getAuthIdentifier() ? (int) $admin->getAuthIdentifier() : null,
        );

        return $this->formatRow($certificate->loadMissing(['user', 'course']));
    }

    /* ------------------------------------------------------------------ *
     |  Internals                                                         |
     * ------------------------------------------------------------------ */

    /** @return array{filename:string,binary:string} */
    private function renderCertificate(UserCertificate $certificate): array
    {
        $learnerName = $certificate->metadata['learner_name']
            ?? $certificate->user?->name
            ?? '—';
        $courseTitle = $certificate->localizedCourseTitle(app()->getLocale());

        $base64 = $this->generateCertificate($courseTitle, $learnerName);
        $safe   = Str::slug($learnerName.'-'.$courseTitle, '_') ?: 'certificate';

        return [
            'filename' => $safe.'.jpg',
            'binary'   => base64_decode($base64),
        ];
    }

    /** @return array{int,?string} [total_issued, last_issued_at_formatted] */
    private function issuedAggregates(): array
    {
        $total  = $this->certificates->countActive();
        $latest = $this->certificates->paginateAdmin(1, null, null)->getCollection()->first();

        return [
            $total,
            $latest instanceof UserCertificate
                ? optional($latest->issued_at)->format('Y-m-d H:i:s')
                : null,
        ];
    }

    /** @return array<string,mixed> */
    private function formatRow(UserCertificate $certificate): array
    {
        $course = $certificate->course;
        $user   = $certificate->user;

        return [
            'id'                 => (int) $certificate->id,
            'uuid'               => $certificate->uuid,
            'certificate_number' => $certificate->certificate_number,
            'status'             => $certificate->status,
            'user_id'            => (int) ($user?->id ?? 0),
            'course_id'          => (int) $certificate->course_id,
            'type'               => $certificate->source_type,
            'employee_id'        => $user?->machine_code ?: ($certificate->metadata['learner_machine_code'] ?? null),
            'learner_name'       => $user?->name ?: ($certificate->metadata['learner_name'] ?? '—'),
            'department'         => $user?->department_name ?? ($certificate->metadata['learner_department'] ?? null),
            'course_title'       => $certificate->localizedCourseTitle(app()->getLocale()) ?: '—',
            'issued_at'          => optional($certificate->issued_at)->format('Y-m-d H:i:s'),
        ];
    }

    /** Persist the uploaded file to the public disk and return its key. */
    private function storeTemplateFile(UploadedFile $file): string
    {
        $name = Str::random(20).md5((string) microtime(true)).'.'.$file->getClientOriginalExtension();

        if (config('filesystems.default') === 's3') {
            $path = Storage::disk('s3')->putFileAs('certificate-templates', $file, $name);
            if ($path === false) {
                abort(500, 'Failed to upload template to S3.');
            }
            return $path;
        }

        Storage::disk('public')->putFileAs('certificate-templates', $file, $name);
        return 'certificate-templates/'.$name;
    }
}
