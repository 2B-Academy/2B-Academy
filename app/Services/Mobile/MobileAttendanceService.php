<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Enums\Mobile\AttendanceMarkFailure;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Course;
use App\Models\User;
use App\Repositories\Contracts\Mobile\MobileAttendanceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Implements the S-06 Mark Present flow.
 *
 * Validation chain (each step short-circuits to a typed failure):
 *
 *   1.  User is enrolled in the course.
 *   2.  There is a candidate session — same course, same cohort,
 *       today, inside the open/grace buffer, with a stored passcode
 *       not yet expired.
 *   3.  Passcode matches (constant-time compare).
 *   4.  Passcode is not expired.
 *   5.  User hasn't already attended this session.
 *
 * On success we DON'T modify any existing service — we insert
 * directly into `attendances` with the same denormalized shape
 * `HelperTrait::saveAttendance` writes (machine_code, department,
 * category snapshot, course snapshot, hours split across sessions).
 * That keeps the legacy reporting screens 100% compatible without
 * touching the legacy MVC code.
 */
final class MobileAttendanceService
{
    public function __construct(
        private readonly MobileAttendanceRepositoryInterface $repository,
        private readonly MobileSettings $settings,
    ) {}

    /**
     * @return array{
     *     success: bool,
     *     failure: ?AttendanceMarkFailure,
     *     session: ?object{
     *         id: int,
     *         course_id: int,
     *         title: ?string,
     *         session_date: ?string,
     *         time_from: ?string,
     *         time_to: ?string,
     *         passcode_expires_at: ?string,
     *     },
     *     attendance_id: ?int,
     * }
     */
    public function markPresent(User $user, Course $course, ?int $sessionId, string $passcode): array
    {
        // Step 0 — machine_code presence. The mobile attendance audit
        // is anchored on the HR-sourced `machine_code`, so a learner
        // without one cannot be marked present without breaking the
        // downstream HR reports. This typically means the user record
        // pre-dates the HR sync and the admin needs to reconcile it.
        if (empty($user->machine_code)) {
            return $this->failure(AttendanceMarkFailure::NotEnrolled);
        }

        // Step 1 — enrolment.
        $cohortId = $this->repository->userCohortIdFor($user, $course->id);
        if ($cohortId <= 0) {
            return $this->failure(AttendanceMarkFailure::NotEnrolled);
        }

        // Step 2 — open session.
        $session = $this->repository->findOpenSessionForUser(
            user: $user,
            courseId: $course->id,
            sessionId: $sessionId,
            now: now(),
            openBufferMinutes: $this->settings->attendanceSessionOpenBufferMinutes(),
            graceBufferMinutes: $this->settings->attendanceSessionGraceMinutes(),
        );
        if ($session === null) {
            return $this->failure(AttendanceMarkFailure::NoOpenWindow);
        }

        // Step 3 — passcode match (constant-time).
        $expected = (string) ($session->passcode ?? '');
        if ($expected === '' || ! hash_equals($expected, $passcode)) {
            return $this->failure(AttendanceMarkFailure::InvalidCode);
        }

        // Step 4 — passcode expiry.
        if ($session->passcode_expires_at !== null
            && \Carbon\Carbon::parse($session->passcode_expires_at)->isPast()
        ) {
            return $this->failure(AttendanceMarkFailure::ExpiredCode);
        }

        // Step 5 — already attended.
        if ($this->repository->hasAttendedSession($user, (int) $session->id)) {
            return $this->failure(AttendanceMarkFailure::AlreadyMarked, $session);
        }

        // Insert the denormalized attendance row + audit log inside
        // a transaction so partial failure is impossible.
        $attendanceId = DB::transaction(function () use ($user, $course, $session, $cohortId) {
            $course->loadMissing('category');

            $sessionsCount = (int) DB::table('course_sessions')
                ->where('course_id', $course->id)
                ->where('section_id', $cohortId)
                ->count();

            $effectiveSessions = $sessionsCount > 0 ? $sessionsCount : 1;
            $attendanceHours   = $effectiveSessions > 1
                ? round(((float) $course->hours) / $effectiveSessions, 2)
                : (float) $course->hours;

            $attendanceId = DB::table('attendances')->insertGetId([
                'user_id'              => $user->id,
                // machine_code is the audit identity — denormalized
                // exactly like the legacy front-attendance flow.
                'user_machine_code'    => $user->machine_code,
                'user_department'      => $user->department_name,
                'course_category_id'   => $course->category?->id,
                'course_category_name' => $course->category?->name,
                'course_id'            => $course->id,
                'course_name'          => $course->title,
                'course_hours'         => $course->hours,
                'section_id'           => $cohortId,
                'session_id'           => $session->id,
                'attendance_hours'     => $attendanceHours,
                'is_manual'            => false,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            AttendanceLog::create([
                'attendance_id' => $attendanceId,
                'user_id'       => $user->id,
                'employee_code' => (string) ($user->machine_code ?? ''),
                'log'           => sprintf(
                    'Mobile passcode mark-present · user=%s · course=%d · session=%d',
                    $user->machine_code ?? Str::limit((string) $user->id, 40),
                    $course->id,
                    $session->id,
                ),
            ]);

            return (int) $attendanceId;
        });

        return [
            'success'              => true,
            'failure'              => null,
            'learner_machine_code' => $user->machine_code,
            'session'              => (object) [
                'id'                  => (int) $session->id,
                'course_id'           => (int) $course->id,
                'title'               => $session->title ?? null,
                'session_date'        => $session->session_date,
                'time_from'           => $session->time_from,
                'time_to'             => $session->time_to,
                'passcode_expires_at' => $session->passcode_expires_at,
            ],
            'attendance_id'        => $attendanceId,
        ];
    }

    /**
     * @param object|null $session
     */
    private function failure(AttendanceMarkFailure $failure, $session = null): array
    {
        return [
            'success'              => false,
            'failure'              => $failure,
            'learner_machine_code' => null,
            'session'              => $session === null ? null : (object) [
                'id'                  => (int) ($session->id ?? 0),
                'course_id'           => (int) ($session->course_id ?? 0),
                'title'               => $session->title ?? null,
                'session_date'        => $session->session_date ?? null,
                'time_from'           => $session->time_from ?? null,
                'time_to'             => $session->time_to ?? null,
                'passcode_expires_at' => $session->passcode_expires_at ?? null,
            ],
            'attendance_id'        => null,
        ];
    }
}
