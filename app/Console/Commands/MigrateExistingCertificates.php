<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Services\CertificateService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * php artisan certificates:migrate-existing
 *
 * One-off (re-runnable) backfill that turns every historical completion
 * into a first-class `user_certificates` row:
 *
 *   - Scans passed final exams on certificate courses (exam source).
 *   - Scans submitted evaluations on evaluation-based certificate
 *     courses (evaluation source), de-duplicated per learner+course.
 *   - Issues through CertificateService so numbering, dedup and the
 *     "one active certificate per learner+course" invariant are shared
 *     with the live issuance hooks.
 *   - Preserves the historical issuance date (uses the source row's
 *     created_at), and processes oldest-first so the per-year
 *     CERT-YYYY-NNNNNN sequence reflects real chronological order.
 *
 * Idempotent: a learner+course that already has an active certificate is
 * skipped, so re-running never duplicates.
 *
 * Options:
 *   --dry-run            Preview without writing.
 *   --employee=CODE      Limit to a single learner (users.machine_code).
 */
class MigrateExistingCertificates extends Command
{
    protected $signature = 'certificates:migrate-existing
        {--dry-run : Show what would be issued without writing}
        {--employee= : Limit to a single employee machine_code}';

    protected $description = 'Backfill first-class user_certificates from historical exam/evaluation completions.';

    public function handle(CertificateService $certificates): int
    {
        $dryRun       = (bool) $this->option('dry-run');
        $employeeCode = $this->option('employee');

        $userId = null;
        if ($employeeCode !== null && $employeeCode !== '') {
            $userId = User::where('machine_code', $employeeCode)->value('id');
            if ($userId === null) {
                $this->error("No user found with machine_code = {$employeeCode}.");
                return self::FAILURE;
            }
        }

        $completions = $this->collectCompletions($userId);

        if ($completions->isEmpty()) {
            $this->info('No eligible completions found — nothing to backfill.');
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($completions as $entry) {
            /** @var UserExam|UserCourseEvaluation $model */
            $model = $entry['model'];

            if ($dryRun) {
                $this->line(sprintf(
                    '  [dry-run] %s #%d → user %d / course %d (%s)',
                    $entry['kind'],
                    $model->id,
                    (int) $model->user_id,
                    (int) $model->course_id,
                    optional($entry['at'])->toDateString() ?? '—',
                ));
                $created++;
                continue;
            }

            $certificate = $entry['kind'] === 'exam'
                ? $certificates->issueFromExam($model)
                : $certificates->issueFromEvaluation($model);

            if ($certificate === null) {
                $skipped++;
                continue;
            }

            $certificate->wasRecentlyCreated ? $created++ : $skipped++;
        }

        $this->info(sprintf(
            '%sIssued: %d · skipped (existing/ineligible): %d · scanned: %d',
            $dryRun ? '[dry-run] ' : '',
            $created,
            $skipped,
            $completions->count(),
        ));

        return self::SUCCESS;
    }

    /**
     * Build the chronological completion list (oldest first). Each course
     * is either exam-based (`is_evaluate = false`) or evaluation-based
     * (`is_evaluate = true`), so the two sources never collide on the same
     * learner+course.
     *
     * @return \Illuminate\Support\Collection<int, array{kind:string, model:mixed, at:?Carbon}>
     */
    private function collectCompletions(?int $userId): \Illuminate\Support\Collection
    {
        $exams = UserExam::query()
            ->with(['course', 'exam', 'user'])
            ->whereHas('course', fn ($q) => $q->where('certificate', true)->where('is_evaluate', false))
            ->whereHas('exam', fn ($q) => $q->where('is_final', true))
            ->where('status', 'success')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get()
            ->map(fn (UserExam $ue) => [
                'kind'  => 'exam',
                'model' => $ue,
                'at'    => $ue->created_at,
            ]);

        $evaluations = UserCourseEvaluation::query()
            ->with(['course', 'user'])
            ->whereHas('course', fn ($q) => $q->where('certificate', true)->where('is_evaluate', true))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get()
            ->unique(fn (UserCourseEvaluation $row) => $row->user_id.'-'.$row->course_id)
            ->map(fn (UserCourseEvaluation $uce) => [
                'kind'  => 'evaluation',
                'model' => $uce,
                'at'    => $uce->created_at,
            ]);

        return $exams
            ->merge($evaluations)
            ->sortBy(fn (array $e) => optional($e['at'])->getTimestamp() ?? 0)
            ->values();
    }
}
