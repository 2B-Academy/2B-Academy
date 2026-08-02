<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UserExam;
use App\Models\UsersCourse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Measures a learner against the certificate rule, and projects whether they
 * are on track to earn a course's certificate BEFORE it is actually issued —
 * the persistent "Certificate: On track" header badge needs this mid-course,
 * not just at completion.
 *
 * This service owns MEASUREMENT only. The rule itself — which metrics matter
 * and at what threshold — belongs to {@see CertificatePolicy}, which reads it
 * from Platform Config. Previously the rule was duplicated here against the
 * per-course `certificate_mode` / `certificate_*_threshold` columns, which no
 * API could set and which disagreed with the values the admin screen was
 * saving; those columns are no longer consulted.
 *
 * Everything that decides or displays certificate eligibility routes through
 * this pair, so there is exactly one place to change the rule.
 */
class CertificateProjectionService
{
    public const STATUS_EARNED   = 'earned';
    public const STATUS_ON_TRACK = 'on_track';
    public const STATUS_AT_RISK  = 'at_risk';
    public const STATUS_BLOCKED  = 'blocked';

    public function __construct(private readonly CertificatePolicy $policy) {}

    public function projectForCourse(User $user, Course $course): array
    {
        if (!$course->certificate) {
            return $this->notOfferedShape();
        }

        $hasCertificate = $this->hasActiveCertificate($user, $course);
        $attendancePercent = $this->attendancePercent($user, $course);
        $scorePercent = $this->scorePercent($user, $course);
        $courseEnded = $this->isCourseEnded($user, $course);

        return $this->decide($course, $hasCertificate, $attendancePercent, $scorePercent, $courseEnded);
    }

    /**
     * Batch variant of projectForCourse — resolves certificates, cohort
     * enrolment, final-exam attempts, and attendance for the WHOLE course
     * list in a handful of queries instead of per-course, so the `my/learnings`
     * dashboard composite (#4) doesn't N+1 across a learner's course list.
     *
     * @param  Collection<int, Course>  $courses
     * @return array<int, array<string, mixed>>  keyed by course_id
     */
    public function projectForCourses(User $user, Collection $courses): array
    {
        $courses = $courses->keyBy('id');
        $courseIds = $courses->keys()->all();

        if (empty($courseIds)) {
            return [];
        }

        $certifiedCourseIds = UserCertificate::query()
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->active()
            ->pluck('course_id')
            ->all();

        $cohortIdsByCourse = UsersCourse::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->whereNotNull('group_id')
            ->pluck('group_id', 'course_id');

        $cohortIds = $cohortIdsByCourse->values()->unique()->all();
        $cohorts = CourseSection::whereIn('id', $cohortIds)->get()->keyBy('id');

        $heldSessionsByCohort = CourseSession::whereIn('section_id', $cohortIds)
            ->ended()
            ->selectRaw('section_id, COUNT(*) as held')
            ->groupBy('section_id')
            ->pluck('held', 'section_id');

        $finalExamsByCourse = CourseExam::whereIn('course_id', $courseIds)
            ->where('is_final', true)
            ->get()
            ->keyBy('course_id');

        $examIds = $finalExamsByCourse->pluck('id')->all();

        $attemptsByExam = UserExam::where('user_id', $user->id)
            ->whereIn('exam_id', $examIds)
            ->get()
            ->keyBy('exam_id');

        $distinctSessionAttendance = Attendance::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->whereNotNull('session_id')
            ->selectRaw('course_id, COUNT(DISTINCT session_id) as attended')
            ->groupBy('course_id')
            ->pluck('attended', 'course_id');

        $rawRowAttendance = Attendance::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->selectRaw('course_id, COUNT(*) as attended')
            ->groupBy('course_id')
            ->pluck('attended', 'course_id');

        $results = [];

        foreach ($courses as $courseId => $course) {
            if (!$course->certificate) {
                $results[$courseId] = $this->notOfferedShape();
                continue;
            }

            $hasCertificate = in_array($courseId, $certifiedCourseIds, true);
            $cohortId = $cohortIdsByCourse->get($courseId);
            $cohort = $cohortId ? $cohorts->get($cohortId) : null;

            $attendancePercent = $this->computeAttendancePercent(
                $course,
                $cohort,
                (int) ($distinctSessionAttendance->get($courseId) ?? 0),
                (int) ($rawRowAttendance->get($courseId) ?? 0),
            );

            $exam = $finalExamsByCourse->get($courseId);
            $attempt = $exam ? $attemptsByExam->get($exam->id) : null;
            $scorePercent = $this->computeScorePercent($exam, $attempt);

            $courseEnded = $this->computeCourseEnded(
                $course,
                $cohort,
                $cohortId ? (int) ($heldSessionsByCohort->get($cohortId) ?? 0) : 0,
            );

            $results[$courseId] = $this->decide($course, $hasCertificate, $attendancePercent, $scorePercent, $courseEnded);
        }

        return $results;
    }

    /**
     * Does the learner MEET the certificate rule right now, on the strength
     * of measured evidence alone?
     *
     * Unlike {@see projectForCourse}, this ignores whether a certificate has
     * already been issued — it answers the raw question "has the bar been
     * cleared?", so it can drive actual issuance without the earned
     * short-circuit making it always-true once a cert exists.
     *
     * Used by the attendance issuance path, which has no completion event of
     * its own: the measurement IS the trigger, so at least one required
     * metric must be measurable. Returns false when the course offers no
     * certificate, when nothing is measurable, or when a threshold is unmet.
     */
    public function meetsIssuanceCriteria(User $user, Course $course): bool
    {
        if (!$course->certificate) {
            return false;
        }

        $attendance = $this->attendancePercent($user, $course);
        $score      = $this->scorePercent($user, $course);

        return $this->policy->hasEvidence($attendance, $score)
            && $this->policy->isSatisfiedBy($attendance, $score);
    }

    /**
     * Does the learner clear the certificate rule, given that a completion
     * event has already fired (final exam passed, evaluation submitted)?
     *
     * Same rule as {@see meetsIssuanceCriteria} minus the evidence
     * requirement: the completion event is itself the evidence, so a metric
     * this course cannot produce (an online course has no sessions, so no
     * attendance %) is skipped rather than treated as a failure. Without
     * that, switching Platform Config to "Attendance only" would silently
     * stop every online course from ever certifying.
     */
    public function satisfiesPolicy(User $user, Course $course): bool
    {
        if (!$course->certificate) {
            return false;
        }

        return $this->policy->isSatisfiedBy(
            $this->attendancePercent($user, $course),
            $this->scorePercent($user, $course),
        );
    }

    /* ------------------------------------------------------------------ *
     |  SHARED DECISION LOGIC                                             |
     * ------------------------------------------------------------------ */

    private function decide(Course $course, bool $hasCertificate, ?int $attendancePercent, ?int $scorePercent, bool $courseEnded): array
    {
        if ($hasCertificate) {
            return array_merge($this->baseShape(), ['status' => self::STATUS_EARNED]);
        }

        // Unmeasurable metrics come back as null and are skipped, so they
        // never push a learner into at_risk for something their course
        // simply cannot report.
        $checks = $this->policy->checks($attendancePercent, $scorePercent);

        $failing = array_keys(array_filter($checks, fn ($meets) => $meets === false));

        $status = self::STATUS_ON_TRACK;
        $blockedReason = null;

        if (!empty($failing)) {
            $status = $courseEnded ? self::STATUS_BLOCKED : self::STATUS_AT_RISK;
            $blockedReason = count($failing) > 1 ? 'both' : $failing[0];
        }

        $message = null;
        if ($status === self::STATUS_BLOCKED) {
            $message = match ($blockedReason) {
                'attendance' => __('messages.certificate_status.blocked_attendance'),
                'score' => __('messages.certificate_status.blocked_score'),
                default => __('messages.certificate_status.blocked_both'),
            };
        }

        return array_merge($this->ruleShape(), [
            'status' => $status,
            'blocked_reason' => $status === self::STATUS_BLOCKED ? $blockedReason : null,
            'message' => $message,
            'attendance_percent' => $attendancePercent,
            'score_percent' => $scorePercent,
        ]);
    }

    /**
     * The configured rule, in the wire shape the learner apps already expect.
     *
     * `certificate_mode` keeps its name for contract stability (Angular and
     * mobile both read it) but now carries the Platform Config basis rather
     * than the retired per-course column.
     *
     * @return array<string, mixed>
     */
    private function ruleShape(): array
    {
        return [
            'certificate_mode'      => $this->policy->basis(),
            'attendance_threshold'  => $this->policy->requires(CertificatePolicy::METRIC_ATTENDANCE)
                ? $this->policy->minAttendance()
                : null,
            'score_threshold'       => $this->policy->requires(CertificatePolicy::METRIC_SCORE)
                ? $this->policy->minScore()
                : null,
        ];
    }

    private function notOfferedShape(): array
    {
        return [
            'status' => null,
            'blocked_reason' => null,
            'message' => null,
            'certificate_mode' => null,
            'attendance_percent' => null,
            'score_percent' => null,
            'attendance_threshold' => null,
            'score_threshold' => null,
        ];
    }

    private function baseShape(): array
    {
        return array_merge($this->ruleShape(), [
            'blocked_reason' => null,
            'message' => null,
            'attendance_percent' => null,
            'score_percent' => null,
        ]);
    }

    /* ------------------------------------------------------------------ *
     |  SINGLE-COURSE DATA RESOLUTION (projectForCourse)                  |
     * ------------------------------------------------------------------ */

    private function hasActiveCertificate(User $user, Course $course): bool
    {
        return UserCertificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->active()
            ->exists();
    }

    /** % of the learner's cohort sessions attended, or null when unmeasurable. */
    private function attendancePercent(User $user, Course $course): ?int
    {
        $cohortId = UsersCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id');

        $cohort = $cohortId ? CourseSection::find($cohortId) : null;

        $distinct = Attendance::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');

        $raw = $distinct === 0
            ? Attendance::where('user_id', $user->id)->where('course_id', $course->id)->count()
            : 0;

        return $this->computeAttendancePercent($course, $cohort, $distinct, $raw);
    }

    /** % of the course's final exam total score reached, or null when not yet attempted. */
    private function scorePercent(User $user, Course $course): ?int
    {
        $exam = CourseExam::where('course_id', $course->id)->where('is_final', true)->first();
        $attempt = $exam ? UserExam::where('user_id', $user->id)->where('exam_id', $exam->id)->first() : null;

        return $this->computeScorePercent($exam, $attempt);
    }

    /** Whether the learner's cohort has run its course (no more chances to improve). */
    private function isCourseEnded(User $user, Course $course): bool
    {
        $cohortId = UsersCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id');

        $cohort = $cohortId ? CourseSection::find($cohortId) : null;
        $held = $cohortId ? CourseSession::where('section_id', $cohortId)->ended()->count() : 0;

        return $this->computeCourseEnded($course, $cohort, $held);
    }

    /* ------------------------------------------------------------------ *
     |  PURE COMPUTATION (shared by both the single and batch paths)      |
     * ------------------------------------------------------------------ */

    private function computeAttendancePercent(Course $course, ?CourseSection $cohort, int $distinctSessionCount, int $rawRowCount): ?int
    {
        $target = $cohort?->number_of_sessions ?? $course->number_of_sessions;

        if (!$target || $target <= 0) {
            return null;
        }

        $attended = $distinctSessionCount > 0 ? $distinctSessionCount : $rawRowCount;

        return (int) min(100, round(($attended / $target) * 100));
    }

    private function computeScorePercent(?CourseExam $exam, ?UserExam $attempt): ?int
    {
        if (!$exam || !$attempt) {
            return null;
        }

        if ($attempt->max_score !== null && $attempt->max_score > 0) {
            return (int) round(($attempt->total_score / $attempt->max_score) * 100);
        }

        if ($exam->degree > 0) {
            return (int) round(($attempt->user_degree / $exam->degree) * 100);
        }

        return null;
    }

    private function computeCourseEnded(Course $course, ?CourseSection $cohort, int $heldSessions): bool
    {
        if ($cohort) {
            $start = $cohort->start_date instanceof Carbon ? $cohort->start_date : ($cohort->start_date ? Carbon::parse($cohort->start_date) : null);
            $end = $cohort->end_date instanceof Carbon ? $cohort->end_date : ($cohort->end_date ? Carbon::parse($cohort->end_date) : null);

            return Course::deriveCohortStatus($cohort->status, $start, $end, $cohort->number_of_sessions, $heldSessions) === 'completed';
        }

        return $course->effectiveStatus() === 'inactive';
    }
}
