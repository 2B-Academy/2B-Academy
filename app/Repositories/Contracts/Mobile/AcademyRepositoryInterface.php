<?php

declare(strict_types=1);

namespace App\Repositories\Contracts\Mobile;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Read surface for the Academy (S-01 → S-04).
 *
 * Every query is dynamic — capacity, deadline, search, category, and
 * the "is already enrolled" flag all come from a SQL join against the
 * live tables, never from a snapshot.
 */
interface AcademyRepositoryInterface
{
    /**
     * Count courses that have at least one cohort the user could still
     * join right now (open seats AND deadline not yet passed).
     *
     * @param int $defaultCloseOffsetDays  cohort start - N days when the
     *                                     cohort has no explicit
     *                                     `enrolment_closes_at` row.
     * @param int $scheduledVisibilityDays how many days before its start
     *                                     a `scheduled` cohort becomes
     *                                     visible. `open_for_enrollment`
     *                                     cohorts ignore this and show
     *                                     immediately.
     */
    public function countAvailableForUser(User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): int;

    /**
     * Per-scope availability counts (`all` / `special` / `general`) for
     * the S-02 filter chips.
     *
     * @return array{all: int, special: int, general: int}
     */
    public function scopeCounts(User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): array;

    /**
     * Paginated list of courses available to the user, optionally
     * filtered by category, free-text search and/or scope
     * (`all` / `special` / `general`).
     *
     * @return LengthAwarePaginator<Course>
     */
    public function paginateAvailable(
        User    $user,
        Carbon  $now,
        int     $defaultCloseOffsetDays,
        int     $scheduledVisibilityDays,
        int     $perPage,
        ?int    $categoryId,
        ?string $search,
        ?string $scope = null,
    ): LengthAwarePaginator;

    /**
     * Hydrate a course for the detail screen — eager-loads category,
     * instructors, qualifications, all sections, sessions, lectures,
     * and rating aggregates. Throws `ModelNotFoundException` if the
     * course doesn't exist.
     */
    public function findForDetail(int $courseId): Course;

    /**
     * Return the *next joinable* cohort for the user, or `null` if
     * none exists. "Joinable" = stored status not `inactive`, start
     * date >= today, enrolment deadline >= today, and seats remaining.
     */
    public function nextJoinableCohort(Course $course, User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): ?CourseSection;

    /**
     * Is the user already enrolled in this course?
     */
    public function isEnrolledInCourse(User $user, int $courseId): bool;

    /**
     * Is the user already enrolled in this specific cohort?
     */
    public function isEnrolledInCohort(User $user, int $cohortId): bool;
}
