<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Models\UsersCourse;
use App\Services\CertificateService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        // ── Attendance-based certificates ───────────────────────────────
        // Session/offline courses whose certificate_mode includes attendance.
        // issueFromAttendance self-checks the threshold, so we simply attempt
        // every enrolment and count what actually mints.
        $attendanceCandidates = $this->collectAttendanceCandidates($userId);
        foreach ($attendanceCandidates as [$user, $course]) {
            if ($dryRun) {
                $this->line(sprintf('  [dry-run] attendance → user %d / course %d', (int) $user->id, (int) $course->id));
                continue;
            }

            $certificate = $certificates->issueFromAttendance($user, $course);
            if ($certificate === null) {
                $skipped++;
                continue;
            }
            $certificate->wasRecentlyCreated ? $created++ : $skipped++;
        }

        $this->info(sprintf(
            '%sIssued: %d · skipped (existing/ineligible): %d · scanned: %d (exam/eval) + %d (attendance)',
            $dryRun ? '[dry-run] ' : '',
            $created,
            $skipped,
            $completions->count(),
            $attendanceCandidates->count(),
        ));

        $this->reportConfigGaps();

        return self::SUCCESS;
    }

    /**
     * Learner+course pairs enrolled in certificate courses whose mode grants a
     * certificate by attendance (attendance | both). Issuance eligibility (the
     * threshold) is decided inside CertificateService::issueFromAttendance.
     *
     * @return \Illuminate\Support\Collection<int, array{0: User, 1: Course}>
     */
    private function collectAttendanceCandidates(?int $userId): \Illuminate\Support\Collection
    {
        $courses = Course::query()
            ->where('certificate', true)
            ->whereIn('certificate_mode', [Course::CERTIFICATE_MODE_ATTENDANCE, Course::CERTIFICATE_MODE_BOTH])
            ->get()
            ->keyBy('id');

        if ($courses->isEmpty()) {
            return collect();
        }

        return UsersCourse::query()
            ->whereIn('course_id', $courses->keys()->all())
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->with('user')
            ->get()
            ->map(fn (UsersCourse $uc) => [$uc->user, $courses->get($uc->course_id)])
            ->filter(fn (array $pair) => $pair[0] !== null && $pair[1] !== null)
            ->values();
    }

    /**
     * Surface courses that have real completions but can never mint a
     * certificate because the course itself isn't configured to grant one —
     * so an admin can flip the flag/mode rather than wonder why the table
     * stays empty.
     */
    private function reportConfigGaps(): void
    {
        $noFlag = DB::table('courses')
            ->where(function ($q) {
                $q->where('certificate', false)->orWhereNull('certificate');
            })
            ->whereExists(function ($q) {
                $q->from('users_courses')->whereColumn('users_courses.course_id', 'courses.id');
            })
            ->count();

        $attendanceButNoMode = DB::table('courses')
            ->where('certificate', true)
            ->where(function ($q) {
                $q->whereNull('certificate_mode')->orWhere('certificate_mode', Course::CERTIFICATE_MODE_SCORE);
            })
            ->whereExists(function ($q) {
                $q->from('course_sessions')->whereColumn('course_sessions.course_id', 'courses.id');
            })
            ->count();

        if ($noFlag > 0) {
            $this->warn("· {$noFlag} enrolled course(s) have certificate=false — they will never issue a certificate until enabled.");
        }
        if ($attendanceButNoMode > 0) {
            $this->warn("· {$attendanceButNoMode} session course(s) have certificate=true but certificate_mode is score/unset — set certificate_mode='attendance' (or 'both') to certify by attendance.");
        }
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
