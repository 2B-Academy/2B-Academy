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
     * filtered by category, free-text search, scope
     * (`all` / `special` / `general`), level, course type, duration
     * bucket and/or job role — and sorted per `$sort`.
     *
     * @param  array<int, string>|null  $levels           Whitelisted `courses.level` values.
     * @param  array<int, string>|null  $courseTypes      Whitelisted `courses.course_type` values.
     * @param  array<int, string>|null  $durationBuckets  `CourseDurationBucket` values.
     * @param  array<int, int>|null     $jobRoleIds       `job_titles.id` values.
     * @param  string                   $sort             `most_relevant|highest_rated|soonest_start|newest`.
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
        ?array  $levels = null,
        ?array  $courseTypes = null,
        ?array  $durationBuckets = null,
        ?array  $jobRoleIds = null,
        string  $sort = 'most_relevant',
    ): LengthAwarePaginator;

    /**
     * Per-option facet counts for the Type / Level / Duration Catalogue
     * filter sections, computed against the exact same "available for
     * this user" predicate as `paginateAvailable()` PLUS every OTHER
     * currently-applied filter (standard faceted-search behaviour: a
     * facet never counts against its own selection). Job Role and
     * Category are excluded — the Figma sidebar renders those without
     * count badges.
     *
     * One grouped aggregate query per facet (three total), never one
     * query per option.
     *
     * @param  array<int, string>|null  $levels
     * @param  array<int, string>|null  $courseTypes
     * @param  array<int, string>|null  $durationBuckets
     * @param  array<int, int>|null     $jobRoleIds
     * @return array{
     *     type: array<string, int>,
     *     level: array<string, int>,
     *     duration: array<string, int>,
     * }
     */
    public function filterFacetCounts(
        User    $user,
        Carbon  $now,
        int     $defaultCloseOffsetDays,
        int     $scheduledVisibilityDays,
        ?int    $categoryId,
        ?string $search,
        ?string $scope,
        ?array  $levels,
        ?array  $courseTypes,
        ?array  $durationBuckets,
        ?array  $jobRoleIds,
    ): array;

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
