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

        if ($this->hasActiveCertificate($user, $course)) {
            return array_merge($this->baseShape($course), ['status' => self::STATUS_EARNED]);
        }

        $mode = $course->certificate_mode ?: Course::CERTIFICATE_MODE_SCORE;
        $attendancePercent = $this->attendancePercent($user, $course);
        $scorePercent = $this->scorePercent($user, $course);
        $courseEnded = $this->isCourseEnded($user, $course);

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

    private function hasActiveCertificate(User $user, Course $course): bool
    {
        return UserCertificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->active()
            ->exists();
    }

    /**
     * % of the learner's cohort sessions attended, or null when it can't
     * be measured (no cohort enrolment, or no session target configured).
     */
    private function attendancePercent(User $user, Course $course): ?int
    {
        $cohortId = UsersCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id');

        $target = $course->number_of_sessions;
        if ($cohortId) {
            $cohortTarget = CourseSection::where('id', $cohortId)->value('number_of_sessions');
            if ($cohortTarget !== null) {
                $target = $cohortTarget;
            }
        }

        if (!$target || $target <= 0) {
            return null;
        }

        $attended = Attendance::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->distinct('session_id')
            ->count('session_id');

        if ($attended === 0) {
            // Fall back to a raw row count for legacy/manual attendance rows
            // that never recorded a session_id.
            $attended = Attendance::where('user_id', $user->id)->where('course_id', $course->id)->count();
        }

        return (int) min(100, round(($attended / $target) * 100));
    }

    /**
     * % of the course's final exam total score reached, or null when the
     * learner hasn't attempted it yet.
     */
    private function scorePercent(User $user, Course $course): ?int
    {
        $exam = CourseExam::where('course_id', $course->id)->where('is_final', true)->first();
        if (!$exam) {
            return null;
        }

        $attempt = UserExam::where('user_id', $user->id)->where('exam_id', $exam->id)->first();
        if (!$attempt) {
            return null;
        }

        // Rich-scored attempt (max_score populated) takes precedence over
        // the legacy degree-based scoring.
        if ($attempt->max_score !== null && $attempt->max_score > 0) {
            return (int) round(($attempt->total_score / $attempt->max_score) * 100);
        }

        if ($exam->degree > 0) {
            return (int) round(($attempt->user_degree / $exam->degree) * 100);
        }

        return null;
    }

    /**
     * Whether the learner has no further opportunity to improve — the
     * cohort has run its course, so a below-threshold metric is final
     * ("blocked") rather than still-improvable ("at risk").
     */
    private function isCourseEnded(User $user, Course $course): bool
    {
        $cohortId = UsersCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id');

        if ($cohortId) {
            $cohort = CourseSection::find($cohortId);
            if ($cohort) {
                $start = $cohort->start_date instanceof Carbon ? $cohort->start_date : ($cohort->start_date ? Carbon::parse($cohort->start_date) : null);
                $end = $cohort->end_date instanceof Carbon ? $cohort->end_date : ($cohort->end_date ? Carbon::parse($cohort->end_date) : null);
                $held = CourseSession::where('course_id', $course->id)->where('section_id', $cohortId)->ended()->count();

                return Course::deriveCohortStatus($cohort->status, $start, $end, $cohort->number_of_sessions, $held) === 'completed';
            }
        }

        return $course->effectiveStatus() === 'inactive';
    }
}
