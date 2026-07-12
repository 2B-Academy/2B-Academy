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
 * Predicts whether a learner is on track to earn a course's certificate
 * BEFORE it is actually issued — the persistent "Certificate: On track"
 * header badge needs this mid-course, not just at completion.
 *
 * This is new business logic built from scratch: prior to this service (and
 * the certificate_mode/certificate_attendance_threshold/certificate_score_threshold
 * columns added alongside it — see migration
 * 2026_07_12_120000_add_certificate_thresholds_to_courses_table), `courses`
 * only had a binary `certificate` flag with no way to configure HOW a
 * learner earns it or by how much. Confirmed absent by grepping every
 * migration/model for attendance-percent/threshold/certificate-mode
 * concepts before adding this — nothing pre-existing was renamed or reused.
 *
 * It never gates real issuance — CertificateService::issueFromExam /
 * issueFromEvaluation remain the sole source of truth for actually minting
 * a certificate. This service only projects toward that outcome.
 */
class CertificateProjectionService
{
    public const STATUS_EARNED   = 'earned';
    public const STATUS_ON_TRACK = 'on_track';
    public const STATUS_AT_RISK  = 'at_risk';
    public const STATUS_BLOCKED  = 'blocked';

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

    /* ------------------------------------------------------------------ *
     |  SHARED DECISION LOGIC                                             |
     * ------------------------------------------------------------------ */

    private function decide(Course $course, bool $hasCertificate, ?int $attendancePercent, ?int $scorePercent, bool $courseEnded): array
    {
        if ($hasCertificate) {
            return array_merge($this->baseShape($course), ['status' => self::STATUS_EARNED]);
        }

        $mode = $course->certificate_mode ?: Course::CERTIFICATE_MODE_SCORE;

        $checks = [];
        if (in_array($mode, [Course::CERTIFICATE_MODE_ATTENDANCE, Course::CERTIFICATE_MODE_BOTH], true)) {
            $checks['attendance'] = $attendancePercent === null ? null : $attendancePercent >= (int) ($course->certificate_attendance_threshold ?? 75);
        }
        if (in_array($mode, [Course::CERTIFICATE_MODE_SCORE, Course::CERTIFICATE_MODE_BOTH], true)) {
            $checks['score'] = $scorePercent === null ? null : $scorePercent >= (int) ($course->certificate_score_threshold ?? 60);
        }

        $failing = array_keys(array_filter($checks, fn ($meets) => $meets === false));

        $status = self::STATUS_ON_TRACK;
        $blockedReason = null;

        if (!empty($failing)) {
            $status = $courseEnded ? self::STATUS_BLOCKED : self::STATUS_AT_RISK;
            $blockedReason = count($failing) === 2 ? 'both' : $failing[0];
        }

        $message = null;
        if ($status === self::STATUS_BLOCKED) {
            $message = match ($blockedReason) {
                'attendance' => __('messages.certificate_status.blocked_attendance'),
                'score' => __('messages.certificate_status.blocked_score'),
                default => __('messages.certificate_status.blocked_both'),
            };
        }

        return [
            'status' => $status,
            'blocked_reason' => $status === self::STATUS_BLOCKED ? $blockedReason : null,
            'message' => $message,
            'certificate_mode' => $mode,
            'attendance_percent' => $attendancePercent,
            'score_percent' => $scorePercent,
            'attendance_threshold' => $course->certificate_attendance_threshold,
            'score_threshold' => $course->certificate_score_threshold,
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

    private function baseShape(Course $course): array
    {
        return [
            'blocked_reason' => null,
            'message' => null,
            'certificate_mode' => $course->certificate_mode,
            'attendance_percent' => null,
            'score_percent' => null,
            'attendance_threshold' => $course->certificate_attendance_threshold,
            'score_threshold' => $course->certificate_score_threshold,
        ];
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
