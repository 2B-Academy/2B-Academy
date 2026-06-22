<?php

declare(strict_types=1);

namespace App\Repositories\Contracts\Mobile;

use App\Models\CourseSession;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Read + record surface for S-06 (Mark Present).
 *
 * The repository owns the SQL for resolving "is there an open
 * attendance window right now for this user on this course?" so the
 * mobile service stays declarative.
 */
interface MobileAttendanceRepositoryInterface
{
    /**
     * Find a session the user could still mark attendance for, given
     * the configured open/grace buffers (in minutes). Returns the
     * single best candidate session, or `null` if none.
     *
     * Candidate criteria:
     *   - session belongs to the course
     *   - session belongs to the user's cohort (users_courses.group_id)
     *   - session_date = today
     *   - now ∈ [time_from − openBuffer, time_to + graceBuffer]
     *   - passcode is not yet expired
     */
    public function findOpenSessionForUser(
        User   $user,
        int    $courseId,
        ?int   $sessionId,
        Carbon $now,
        int    $openBufferMinutes,
        int    $graceBufferMinutes,
    ): ?CourseSession;

    /**
     * Has the user already recorded attendance for this session?
     */
    public function hasAttendedSession(User $user, int $sessionId): bool;

    /**
     * Return the *user's* cohort id for this course (0 if unenrolled).
     */
    public function userCohortIdFor(User $user, int $courseId): int;
}
