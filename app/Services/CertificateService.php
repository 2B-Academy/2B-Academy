<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Repositories\Contracts\UserCertificateRepositoryInterface;
use DateTimeInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * First-class Certificate domain service (2026 redesign).
 *
 * A certificate is a standalone business document, NOT a computed view
 * of exam/evaluation completion. All issuance, numbering, deduplication
 * and revocation flow through this single service so the rules live in
 * exactly one place (never scattered across controllers).
 *
 * Issuance rules:
 *   A) final-exam pass  ? course.certificate && !course.is_evaluate
 *                         && exam.is_final && user_exam.status='success'
 *   B) evaluation done  ? course.certificate &&  course.is_evaluate
 *
 * Invariant: one learner + one course = one ACTIVE certificate.
 */
class CertificateService
{
    public function __construct(
        private readonly UserCertificateRepositoryInterface $repo,
    ) {}

    /* ================================================================ *
     |  Issuance                                                        |
     * ================================================================ */

    /**
     * Issue a certificate for a passed final exam. Returns the existing
     * active certificate (idempotent) or null when the exam is not
     * certificate-eligible.
     */
    public function issueFromExam(UserExam $exam): ?UserCertificate
    {
        $exam->loadMissing(['course', 'exam', 'user']);

        $course  = $exam->course;
        $examDef = $exam->exam;

        if (!$course || !$examDef) {
            return null;
        }
        if (!$course->certificate || $course->is_evaluate) {
            return null;
        }
        if (!$examDef->is_final || $exam->status !== 'success') {
            return null;
        }

        return $this->issue(
            userId: (int) $exam->user_id,
            courseId: (int) $course->id,
            sourceType: UserCertificate::SOURCE_EXAM,
            sourceId: (int) $exam->id,
            issuedAt: $exam->created_at ?? now(),
            issuedBy: null,
            metadata: $this->snapshot($exam->user, $course, [
                'source'            => UserCertificate::SOURCE_EXAM,
                'exam_total_degree' => $examDef->degree,
                'user_degree'       => $exam->user_degree,
            ]),
        );
    }

    /**
     * Issue a certificate for a completed evaluation-based course.
     * Idempotent; returns null when the course is not certificate-eligible.
     */
    public function issueFromEvaluation(UserCourseEvaluation $evaluation): ?UserCertificate
    {
        $evaluation->loadMissing(['course', 'user']);

        $course = $evaluation->course;

        if (!$course) {
            return null;
        }
        if (!$course->certificate || !$course->is_evaluate) {
            return null;
        }

        return $this->issue(
            userId: (int) $evaluation->user_id,
            courseId: (int) $course->id,
            sourceType: UserCertificate::SOURCE_EVALUATION,
            sourceId: (int) $evaluation->id,
            issuedAt: $evaluation->created_at ?? now(),
            issuedBy: null,
            metadata: $this->snapshot($evaluation->user, $course, [
                'source' => UserCertificate::SOURCE_EVALUATION,
            ]),
        );
    }

    /* ================================================================ *
     |  Lifecycle                                                       |
     * ================================================================ */

    public function revoke(UserCertificate $certificate, ?int $revokedBy = null): UserCertificate
    {
        if ($certificate->isRevoked()) {
            return $certificate;
        }

        $certificate->update([
            'status'     => UserCertificate::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_by' => $revokedBy,
        ]);

        return $certificate->refresh();
    }

    public function findById(int $id): ?UserCertificate
    {
        return $this->repo->findById($id);
    }

    /* ================================================================ *
     |  Legacy read API (kept for /api/v1/certificates) — DB-backed     |
     * ================================================================ */

    public function paginate(int $perPage = 20, ?int $courseId = null): LengthAwarePaginator
    {
        $paginator = $this->repo->paginateAdmin($perPage, request()->input('search'), $courseId);
        $paginator->getCollection()->transform(fn (UserCertificate $c) => $this->toArray($c));

        return $paginator;
    }

    /** @return array<int, array<string, mixed>> */
    public function findByCourse(int $courseId): array
    {
        return $this->repo->paginateAdmin(1000, null, $courseId)
            ->getCollection()
            ->map(fn (UserCertificate $c) => $this->toArray($c))
            ->all();
    }

    /* ================================================================ *
     |  Internals                                                       |
     * ================================================================ */

    /**
     * Core issuance routine — transactional, idempotent (one active
     * certificate per learner+course), and number-safe under concurrency.
     */
    private function issue(
        int $userId,
        int $courseId,
        string $sourceType,
        ?int $sourceId,
        DateTimeInterface $issuedAt,
        ?int $issuedBy,
        array $metadata,
    ): UserCertificate {
        return DB::transaction(function () use ($userId, $courseId, $sourceType, $sourceId, $issuedAt, $issuedBy, $metadata) {
            $existing = $this->repo->findActiveByUserAndCourse($userId, $courseId);
            if ($existing) {
                return $existing;
            }

            $number = $this->generateNumber((int) $issuedAt->format('Y'));

            return $this->repo->create([
                'user_id'            => $userId,
                'course_id'          => $courseId,
                'source_type'        => $sourceType,
                'source_id'          => $sourceId,
                'certificate_number' => $number,
                'status'             => UserCertificate::STATUS_ACTIVE,
                'issued_at'          => $issuedAt,
                'issued_by'          => $issuedBy,
                'metadata'           => $metadata,
            ]);
        });
    }

    /** Sequential, per-year, zero-padded: CERT-2026-000001. */
    private function generateNumber(int $year): string
    {
        $latest = $this->repo->latestNumberForYear($year);

        $seq = 0;
        if ($latest !== null && preg_match('/CERT-'.$year.'-(\d+)/', $latest, $m)) {
            $seq = (int) $m[1];
        }

        return sprintf('CERT-%d-%06d', $year, $seq + 1);
    }

    /**
     * Immutable snapshot of the learner + course identity at issuance
     * time, so the historical document never shifts if HR/course data
     * changes later.
     *
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function snapshot(?User $user, Course $course, array $extra = []): array
    {
        return array_merge([
            'learner_name'         => $user?->name,
            'learner_machine_code' => $user?->machine_code,
            'learner_department'   => $user?->department_name,
            'course_title'         => [
                'en' => $course->getTranslation('title_for_certificate', 'en') ?: $course->getTranslation('title', 'en'),
                'ar' => $course->getTranslation('title_for_certificate', 'ar') ?: $course->getTranslation('title', 'ar'),
            ],
        ], $extra);
    }

    /** @return array<string, mixed> */
    private function toArray(UserCertificate $certificate): array
    {
        $user   = $certificate->user;
        $course = $certificate->course;

        return [
            'id'                 => (int) $certificate->id,
            'uuid'               => $certificate->uuid,
            'certificate_number' => $certificate->certificate_number,
            'status'             => $certificate->status,
            'type'               => $certificate->source_type,
            'user'               => [
                'id'              => $user?->id,
                'name'            => $user?->name,
                'machine_code'    => $user?->machine_code,
                'department_name' => $user?->department_name,
            ],
            'course'             => [
                'id'                    => $course?->id,
                'title'                 => $course?->getTranslation('title', app()->getLocale()),
                'title_for_certificate' => $course?->getTranslation('title_for_certificate', app()->getLocale()),
            ],
            'issued_at'          => optional($certificate->issued_at)->format('Y-m-d H:i:s'),
            'created_at'         => optional($certificate->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
