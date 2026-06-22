<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents\Mobile;

use App\Models\CourseSection;
use App\Models\User;
use App\Repositories\Contracts\Mobile\MobileEnrolmentRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Persistence side of the S-03 → S-04 enrolment race.
 *
 * The seat count is read inside a `SELECT … FOR UPDATE` row lock on
 * the cohort, so two simultaneous enrol requests serialize on the
 * database side and the second one sees the first's row before it
 * decides whether the cohort is full.
 *
 * The actual insert into `users_courses` is keyed on (user_id,
 * course_id) — the table's unique index protects against double
 * inserts even if a buggy client retries the same call.
 */
final class MobileEnrolmentRepository implements MobileEnrolmentRepositoryInterface
{
    public function lockAndCountSeats(int $cohortId): int
    {
        // Lock the cohort row first so the count and any subsequent
        // insert form a single critical section.
        DB::table('course_sections')
            ->where('id', $cohortId)
            ->lockForUpdate()
            ->first();

        return (int) DB::table('users_courses')
            ->where('group_id', $cohortId)
            ->count();
    }

    public function createEnrolment(User $user, CourseSection $cohort): void
    {
        DB::table('users_courses')->updateOrInsert(
            ['user_id' => $user->id, 'course_id' => $cohort->course_id],
            [
                'group_id'   => $cohort->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
}
