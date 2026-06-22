<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents\Mobile;

use App\Models\CourseSession;
use App\Models\User;
use App\Repositories\Contracts\Mobile\MobileAttendanceRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of the Mark Present (S-06) lookups.
 *
 * "Open session for the user" means:
 *   - belongs to the course
 *   - belongs to the user's cohort (users_courses.group_id)
 *   - has a session_date == today (or null + open passcode)
 *   - now ∈ [time_from - openBuffer, time_to + graceBuffer]
 *   - passcode is set AND passcode_expires_at > now (when present)
 */
final class MobileAttendanceRepository implements MobileAttendanceRepositoryInterface
{
    public function __construct(private readonly CourseSession $session) {}

    public function findOpenSessionForUser(
        User   $user,
        int    $courseId,
        ?int   $sessionId,
        Carbon $now,
        int    $openBufferMinutes,
        int    $graceBufferMinutes,
    ): ?CourseSession {
        $cohortId = $this->userCohortIdFor($user, $courseId);

        if ($cohortId <= 0) {
            return null;
        }

        $today    = $now->toDateString();
        $nowTime  = $now->format('H:i:s');
        $openBuf  = max(0, $openBufferMinutes);
        $graceBuf = max(0, $graceBufferMinutes);

        $query = $this->session->newQuery()
            ->where('course_id', $courseId)
            ->where('section_id', $cohortId)
            ->where(function ($q) use ($today) {
                $q->whereNull('session_date')
                  ->orWhereDate('session_date', $today);
            })
            ->where(function ($q) use ($nowTime, $openBuf, $graceBuf) {
                // Either there is no time window (just date-based), or
                // we're inside the configured [time_from - openBuf, time_to + graceBuf] window.
                $q->where(function ($q2) {
                    $q2->whereNull('time_from')->whereNull('time_to');
                })->orWhere(function ($q2) use ($nowTime, $openBuf, $graceBuf) {
                    // `time_from`/`time_to` are TIME columns; TIMESTAMPDIFF on
                    // time-only values yields NULL (which silently excludes the
                    // row), so compare on the clock directly instead.
                    $q2->whereRaw(
                        'time_from <= ADDTIME(?, SEC_TO_TIME(? * 60))',
                        [$nowTime, $openBuf],
                    )->whereRaw(
                        'time_to >= SUBTIME(?, SEC_TO_TIME(? * 60))',
                        [$nowTime, $graceBuf],
                    );
                });
            })
            // Passcode must be set and still valid. If passcode_expires_at
            // is NULL we trust the session_date/time window alone, but
            // a stored passcode IS required for the mark-present flow.
            ->whereNotNull('passcode')
            ->where(function ($q) use ($now) {
                $q->whereNull('passcode_expires_at')
                  ->orWhere('passcode_expires_at', '>=', $now);
            });

        if ($sessionId !== null) {
            $query->where('id', $sessionId);
        }

        return $query
            ->orderBy('session_date')
            ->orderBy('time_from')
            ->orderBy('id')
            ->first();
    }

    public function hasAttendedSession(User $user, int $sessionId): bool
    {
        return DB::table('attendances')
            ->where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->exists();
    }

    public function userCohortIdFor(User $user, int $courseId): int
    {
        return (int) DB::table('users_courses')
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->value('group_id');
    }
}
