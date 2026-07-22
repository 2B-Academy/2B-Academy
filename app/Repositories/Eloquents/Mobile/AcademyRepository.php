<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents\Mobile;

use App\Enums\EnumRegistry;
use App\Enums\Mobile\CourseDurationBucket;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Repositories\Contracts\Mobile\AcademyRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of the Academy read surface (S-01 → S-04).
 *
 * Concept: a course is "available for the user" iff it has at least
 * one cohort (course_sections row) that is:
 *
 *   1.  `stored status NOT 'inactive'`        — admin override veto
 *   2.  `start_date >= today`                  — future cohort
 *   3.  `effective enrolment_closes_at >= today` where the effective
 *        deadline is the persisted `enrolment_closes_at` if present,
 *        otherwise `start_date - mobile.academy.default_close_offset_days`.
 *   4.  `enrolled_count < capacity`            — seats remaining
 *   5.  user is not already enrolled in *any* cohort of the course
 *
 * Everything below is expressed as raw SQL inside `whereExists`
 * subqueries so the filter pushes down to the database and a single
 * paginated query produces the list without N+1.
 */
final class AcademyRepository implements AcademyRepositoryInterface
{
    public function __construct(
        private readonly Course $course,
        private readonly CourseSection $section,
    ) {}

    public function countAvailableForUser(User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): int
    {
        return $this->baseAvailableQuery($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays)->count();
    }

    /**
     * Per-scope availability counts that feed the S-02 chips:
     *   - all     → every joinable course for this user
     *   - special → joinable courses whose qualification skills overlap
     *               the employee's job-title required skills
     *   - general → joinable courses flagged `for_public`
     *
     * Each count reuses the exact same "available for this user"
     * predicate as the paginated list, so the badge numbers always
     * agree with what the list actually renders.
     *
     * @return array{all: int, special: int, general: int}
     */
    public function scopeCounts(?User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): array
    {
        $skillIds = $this->employeeQualificationSkillIds($user);

        $special = $this->baseAvailableQuery($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays);
        $this->applyScope($special, 'special', $skillIds);

        $general = $this->baseAvailableQuery($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays);
        $this->applyScope($general, 'general', $skillIds);

        return [
            'all'     => $this->baseAvailableQuery($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays)->count(),
            'special' => $special->count(),
            'general' => $general->count(),
        ];
    }

    public function paginateAvailable(
        ?User   $user,
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
    ): LengthAwarePaginator {
        $today = $now->toDateString();

        $query = $this->baseAvailableQuery($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays);
        $this->applyScope($query, $scope, $this->employeeQualificationSkillIds($user));
        $this->applyLevelFilter($query, $levels);
        $this->applyCourseTypeFilter($query, $courseTypes);
        $this->applyJobRoleFilter($query, $jobRoleIds);
        $this->applyDurationFilter($query, $durationBuckets, $today);
        $this->applyCategoryAndSearch($query, $categoryId, $search);

        $query
            ->select([
                'courses.id',
                'courses.title',
                'courses.description',
                'courses.course_type',
                'courses.category_id',
                'courses.image',
                'courses.hours',
                'courses.level',
                'courses.certificate',
                'courses.created_at',
            ])
            // Course-level "X weeks" stat (Catalogue card + Course Detail).
            // See CourseDurationBucket for why this is derived from the
            // next dated cohort rather than a stored column.
            ->selectRaw($this->durationWeeksSql() . ' as duration_weeks', [$today])
            ->with([
                'category:id,name',
                'qualificationSkills:id,name',
                'instructors:id,name,image',
                // Eager-load only the sections we need to surface the
                // next cohort. The repository is responsible for
                // pre-trimming so the resource doesn't have to.
                'sections' => fn ($q) => $q
                    ->orderBy('start_date')
                    ->select(['id', 'course_id', 'name', 'start_date', 'end_date', 'capacity', 'enrolment_closes_at', 'status']),
            ])
            ->withCount(['ratings as rating_count'])
            ->withAvg('ratings as rating_avg', 'rating');

        $this->applySort($query, $sort, $today);

        return $query->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function filterFacetCounts(
        ?User   $user,
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
    ): array {
        $today    = $now->toDateString();
        $skillIds = $this->employeeQualificationSkillIds($user);

        $baseFacetQuery = function () use ($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays, $categoryId, $search, $scope, $skillIds): Builder {
            $query = $this->baseAvailableQuery($user, $now, $defaultCloseOffsetDays, $scheduledVisibilityDays);
            $this->applyScope($query, $scope, $skillIds);
            $this->applyCategoryAndSearch($query, $categoryId, $search);

            return $query;
        };

        // Type facet — every OTHER filter (level/duration/job role) applies, grouped by course_type.
        $typeQuery = $baseFacetQuery();
        $this->applyLevelFilter($typeQuery, $levels);
        $this->applyJobRoleFilter($typeQuery, $jobRoleIds);
        $this->applyDurationFilter($typeQuery, $durationBuckets, $today);
        $typeCounts = $typeQuery
            ->select('courses.course_type')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('courses.course_type')
            ->pluck('aggregate', 'courses.course_type');

        // Level facet — every OTHER filter (type/duration/job role) applies, grouped by level.
        $levelQuery = $baseFacetQuery();
        $this->applyCourseTypeFilter($levelQuery, $courseTypes);
        $this->applyJobRoleFilter($levelQuery, $jobRoleIds);
        $this->applyDurationFilter($levelQuery, $durationBuckets, $today);
        $levelCounts = $levelQuery
            ->select('courses.level')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('courses.level')
            ->pluck('aggregate', 'courses.level');

        // Duration facet — every OTHER filter (type/level/job role) applies, grouped by the raw
        // week count (cheap: distinct week values, not distinct courses), folded into buckets below.
        $durationQuery = $baseFacetQuery();
        $this->applyLevelFilter($durationQuery, $levels);
        $this->applyCourseTypeFilter($durationQuery, $courseTypes);
        $this->applyJobRoleFilter($durationQuery, $jobRoleIds);
        $durationRows = $durationQuery
            ->selectRaw($this->durationWeeksSql() . ' as weeks', [$today])
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('weeks')
            ->pluck('aggregate', 'weeks');

        $durationCounts = array_fill_keys(CourseDurationBucket::values(), 0);
        foreach ($durationRows as $weeks => $count) {
            $bucket = CourseDurationBucket::fromWeeks($weeks !== null ? (int) $weeks : null);
            if ($bucket !== null) {
                $durationCounts[$bucket->value] += (int) $count;
            }
        }

        return [
            'type'     => $this->zeroFillFacet(EnumRegistry::values('course_type'), $typeCounts),
            'level'    => $this->zeroFillFacet(EnumRegistry::values('course_level'), $levelCounts),
            'duration' => $durationCounts,
        ];
    }

    public function findForDetail(int $courseId): Course
    {
        $today = now()->toDateString();

        return $this->course->newQuery()
            ->select('courses.*')
            // Same single-source-of-truth formula as the list query
            // (`durationWeeksSql()`) so the S-03 "X weeks" stat always
            // agrees with the S-02 card/filter for the same course.
            ->selectRaw($this->durationWeeksSql() . ' as duration_weeks', [$today])
            ->with([
                'category:id,name',
                'instructors:id,name,image,bio',
                'qualificationSkills:id,name',
                'sections' => fn ($q) => $q
                    ->orderBy('start_date')
                    ->withCount(['enrollments as enrolled_count']),
                'sections.sessions' => fn ($q) => $q
                    ->orderBy('session_date')
                    ->orderBy('time_from'),
                // Lectures drive the S-03 "Course Content" block.
                'lectures' => fn ($q) => $q->orderBy('id'),
            ])
            ->withCount([
                'users as users_count',
                'ratings as rating_count',
            ])
            ->withAvg('ratings as rating_avg', 'rating')
            ->findOrFail($courseId);
    }

    public function nextJoinableCohort(Course $course, ?User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): ?CourseSection
    {
        $today      = $now->toDateString();
        $offset     = max(0, $defaultCloseOffsetDays);
        $visibility = max(0, $scheduledVisibilityDays);

        return $this->section->newQuery()
            ->withCount(['enrollments as enrolled_count'])
            ->where('course_id', $course->id)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'inactive');
            })
            ->whereNotNull('start_date')
            ->whereDate('start_date', '>=', $today)
            // App-visibility gate: `open_for_enrollment` shows regardless of
            // how far out it starts; anything else (scheduled) only surfaces
            // once it is within `$visibility` days of its start date.
            ->where(function ($q) use ($today, $visibility) {
                $q->where('status', 'open_for_enrollment')
                  ->orWhereRaw('DATE_SUB(start_date, INTERVAL ? DAY) <= ?', [$visibility, $today]);
            })
            ->where(function ($q) use ($today, $offset) {
                // Effective deadline = enrolment_closes_at OR start_date - offset.
                $q->where(function ($q2) use ($today) {
                    $q2->whereNotNull('enrolment_closes_at')
                       ->whereDate('enrolment_closes_at', '>=', $today);
                })->orWhere(function ($q2) use ($today, $offset) {
                    $q2->whereNull('enrolment_closes_at')
                       ->whereRaw('DATE_SUB(start_date, INTERVAL ? DAY) >= ?', [$offset, $today]);
                });
            })
            // Capacity check: capacity NULL => unlimited; otherwise
            // enrolled_count must be < capacity.
            ->where(function ($q) {
                $q->whereNull('capacity')
                  ->orWhereRaw('(SELECT COUNT(*) FROM users_courses uc WHERE uc.group_id = course_sections.id) < course_sections.capacity');
            })
            // Skip cohorts the user is already in (a guest has none).
            ->when($user !== null, fn ($q) => $q->whereNotExists(function ($sub) use ($user) {
                $sub->from('users_courses')
                    ->whereColumn('users_courses.group_id', 'course_sections.id')
                    ->where('users_courses.user_id', $user->id);
            }))
            ->orderBy('start_date')
            ->orderBy('id')
            ->first();
    }

    public function isEnrolledInCourse(User $user, int $courseId): bool
    {
        return DB::table('users_courses')
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->exists();
    }

    public function isEnrolledInCohort(User $user, int $cohortId): bool
    {
        return DB::table('users_courses')
            ->where('user_id', $user->id)
            ->where('group_id', $cohortId)
            ->exists();
    }

    // ────────────────────────────────────────────────────────────
    // Internals
    // ────────────────────────────────────────────────────────────

    /**
     * Narrow an "available courses" query to a single S-02 scope.
     *
     * Special and General form a TRUE PARTITION of the available set, so
     * the chip badges always reconcile: `all = special + general`.
     *
     *   - `special`  → course shares at least one qualification skill
     *                  with the employee's job-title required skills.
     *                  When the employee has no qualifications nothing is
     *                  special (`1 = 0`).
     *   - `general`  → every available course that is NOT special (the
     *                  exact complement). When the employee has no
     *                  qualifications, all available courses are general.
     *   - anything else (incl. `all`/null) → no extra predicate.
     *
     * @param  Builder|QueryBuilder  $q
     * @param  array<int, int>       $skillIds
     * @return Builder|QueryBuilder
     */
    private function applyScope(Builder|QueryBuilder $q, ?string $scope, array $skillIds): Builder|QueryBuilder
    {
        if ($scope === 'special') {
            if (empty($skillIds)) {
                return $q->whereRaw('1 = 0');
            }

            return $q->whereExists(function ($sub) use ($skillIds) {
                $sub->from('course_qualification_skills')
                    ->whereColumn('course_qualification_skills.course_id', 'courses.id')
                    ->whereIn('course_qualification_skills.qualification_skill_id', $skillIds);
            });
        }

        if ($scope === 'general') {
            // Complement of `special`: anything not tied to the employee's
            // qualification skills. With no skills, every course is general.
            if (empty($skillIds)) {
                return $q;
            }

            return $q->whereNotExists(function ($sub) use ($skillIds) {
                $sub->from('course_qualification_skills')
                    ->whereColumn('course_qualification_skills.course_id', 'courses.id')
                    ->whereIn('course_qualification_skills.qualification_skill_id', $skillIds);
            });
        }

        return $q;
    }

    /**
     * Whitelist + apply the `level` filter (`courses.level`).
     *
     * @param  Builder|QueryBuilder     $q
     * @param  array<int, string>|null  $levels
     * @return Builder|QueryBuilder
     */
    private function applyLevelFilter(Builder|QueryBuilder $q, ?array $levels): Builder|QueryBuilder
    {
        if (empty($levels)) {
            return $q;
        }

        return $q->whereIn('courses.level', $levels);
    }

    /**
     * Whitelist + apply the `type` filter (`courses.course_type`).
     *
     * @param  Builder|QueryBuilder     $q
     * @param  array<int, string>|null  $courseTypes
     * @return Builder|QueryBuilder
     */
    private function applyCourseTypeFilter(Builder|QueryBuilder $q, ?array $courseTypes): Builder|QueryBuilder
    {
        if (empty($courseTypes)) {
            return $q;
        }

        return $q->whereIn('courses.course_type', $courseTypes);
    }

    /**
     * Job Role filter. There is no direct Course↔JobTitle relation — the
     * join path is Course → QualificationSkill (`course_qualification_skills`)
     * → JobTitle (`job_title_qualification_skill`). No counts are needed
     * for this filter (Figma renders Job Role as plain chips), so this is
     * only ever used to narrow the list query, never for facet counting.
     *
     * @param  Builder|QueryBuilder  $q
     * @param  array<int, int>|null  $jobRoleIds
     * @return Builder|QueryBuilder
     */
    private function applyJobRoleFilter(Builder|QueryBuilder $q, ?array $jobRoleIds): Builder|QueryBuilder
    {
        if (empty($jobRoleIds)) {
            return $q;
        }

        return $q->whereExists(function ($sub) use ($jobRoleIds) {
            $sub->from('course_qualification_skills as cqs')
                ->join('job_title_qualification_skill as jtqs', 'jtqs.qualification_skill_id', '=', 'cqs.qualification_skill_id')
                ->whereColumn('cqs.course_id', 'courses.id')
                ->whereIn('jtqs.job_title_id', $jobRoleIds);
        });
    }

    /**
     * Apply the bucketed `duration` filter. Buckets are OR'd together
     * (a course matches if its computed `duration_weeks` falls in ANY of
     * the selected buckets); the boundary numbers always come from
     * `CourseDurationBucket::range()`, never re-hardcoded here.
     *
     * @param  Builder|QueryBuilder     $q
     * @param  array<int, string>|null  $durationBuckets
     * @return Builder|QueryBuilder
     */
    private function applyDurationFilter(Builder|QueryBuilder $q, ?array $durationBuckets, string $today): Builder|QueryBuilder
    {
        if (empty($durationBuckets)) {
            return $q;
        }

        $buckets = array_values(array_filter(array_map(
            static fn (string $value) => CourseDurationBucket::tryFrom($value),
            $durationBuckets,
        )));

        if (empty($buckets)) {
            return $q;
        }

        $sql      = $this->durationWeeksSql();
        $clauses  = [];
        $bindings = [];

        foreach ($buckets as $bucket) {
            [$min, $max] = $bucket->range();
            $bindings[]  = $today;

            if ($max === null) {
                $clauses[]  = "({$sql} >= ?)";
                $bindings[] = $min;
            } else {
                $clauses[]  = "({$sql} BETWEEN ? AND ?)";
                $bindings[] = $min;
                $bindings[] = $max;
            }
        }

        return $q->whereRaw('(' . implode(' OR ', $clauses) . ')', $bindings);
    }

    private function applyCategoryAndSearch(Builder $q, ?int $categoryId, ?string $search): Builder
    {
        $locale = app()->getLocale();

        return $q
            ->when($categoryId, fn ($inner) => $inner->where('courses.category_id', $categoryId))
            ->when($search, fn ($inner) => $inner->where(function (Builder $orGroup) use ($search, $locale) {
                $orGroup->where("courses.title->{$locale}", 'LIKE', "%{$search}%")
                        ->orWhere('courses.title->en', 'LIKE', "%{$search}%")
                        ->orWhere('courses.title->ar', 'LIKE', "%{$search}%");
            }));
    }

    private function applySort(Builder $q, string $sort, string $today): Builder
    {
        return match ($sort) {
            'newest'        => $q->orderByDesc('courses.created_at'),
            'highest_rated' => $q->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            'soonest_start' => $q->orderByRaw(
                $this->nextStartDateSql() . ' IS NULL, ' . $this->nextStartDateSql() . ' ASC',
                [$today, $today],
            ),
            // `most_relevant` (default) — the original, unchanged ordering.
            default => $q->orderByDesc('courses.id'),
        };
    }

    /**
     * Scalar subquery: the computed calendar span (in weeks) of the
     * earliest still-upcoming, non-inactive, fully-dated cohort for this
     * course. Single source of the "which cohort / which formula" used
     * both for the `duration_weeks` field (SELECT) and the `duration`
     * filter + facet counts (WHERE/GROUP BY) — only the bucket BOUNDARY
     * numbers live outside this method, in `CourseDurationBucket::range()`.
     *
     * Bound with exactly one `?` (today's date) per appearance.
     */
    private function durationWeeksSql(): string
    {
        return '(SELECT CEIL((DATEDIFF(cs_dur.end_date, cs_dur.start_date) + 1) / 7)
                  FROM course_sections cs_dur
                  WHERE cs_dur.course_id = courses.id
                    AND (cs_dur.status IS NULL OR cs_dur.status != "inactive")
                    AND cs_dur.start_date IS NOT NULL
                    AND cs_dur.end_date IS NOT NULL
                    AND cs_dur.start_date >= ?
                  ORDER BY cs_dur.start_date ASC
                  LIMIT 1)';
    }

    /**
     * Scalar subquery: the earliest still-upcoming, non-inactive cohort
     * start date for this course — powers the `soonest_start` sort.
     * Bound with exactly one `?` (today's date) per appearance.
     */
    private function nextStartDateSql(): string
    {
        return '(SELECT MIN(cs_sort.start_date)
                  FROM course_sections cs_sort
                  WHERE cs_sort.course_id = courses.id
                    AND (cs_sort.status IS NULL OR cs_sort.status != "inactive")
                    AND cs_sort.start_date >= ?)';
    }

    /**
     * Zero-fill every whitelisted enum value so the client always gets a
     * stable set of keys (an option with zero matches still needs to
     * render, just disabled/greyed).
     *
     * @param  array<int, string>              $allowedValues
     * @param  Collection<string, int|string>  $counts
     * @return array<string, int>
     */
    private function zeroFillFacet(array $allowedValues, Collection $counts): array
    {
        $result = array_fill_keys($allowedValues, 0);

        foreach ($counts as $key => $count) {
            if ($key !== null && array_key_exists((string) $key, $result)) {
                $result[(string) $key] = (int) $count;
            }
        }

        return $result;
    }

    /**
     * The qualification-skill IDs an employee is expected to hold,
     * derived from their job title (`job_title_qualification_skill`).
     * Returns an empty array when the employee has no job title — which
     * makes the "Special" scope correctly resolve to zero results.
     *
     * @return array<int, int>
     */
    private function employeeQualificationSkillIds(?User $user): array
    {
        if (empty($user?->job_title_id)) {
            return [];
        }

        return DB::table('job_title_qualification_skill')
            ->where('job_title_id', $user->job_title_id)
            ->pluck('qualification_skill_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function baseAvailableQuery(?User $user, Carbon $now, int $defaultCloseOffsetDays, int $scheduledVisibilityDays): Builder
    {
        $today      = $now->toDateString();
        $offset     = max(0, $defaultCloseOffsetDays);
        $visibility = max(0, $scheduledVisibilityDays);

        $query = $this->course->newQuery()
            ->where(function ($q) use ($user, $today, $offset, $visibility) {
                $this->applyAvailableExists($q, $user, $today, $offset, $visibility);
            });

        // Guest (unauthenticated) catalogue: only truly public courses — those
        // with NO qualification skills assigned. A course tied to any
        // qualification is role-specific and belongs to a logged-in learner's
        // Special/General split, so it must never surface to a guest. There
        // are no enrolments to exclude for a guest.
        if ($user === null) {
            return $query->whereNotExists(function ($sub) {
                $sub->from('course_qualification_skills')
                    ->whereColumn('course_qualification_skills.course_id', 'courses.id');
            });
        }

        // Rule #5 — drop the whole course once the user is enrolled in
        // ANY of its cohorts. The per-cohort exclusion inside
        // `applyAvailableExists` only hides the joined cohort, so a
        // course with a second joinable cohort would otherwise keep
        // showing in the Academy list after enrolment.
        return $query->whereNotExists(function ($sub) use ($user) {
            $sub->from('users_courses')
                ->whereColumn('users_courses.course_id', 'courses.id')
                ->where('users_courses.user_id', $user->id);
        });
    }

    /**
     * Add the "joinable cohort exists for this user" predicate.
     * Extracted so countAvailableForUser and paginateAvailable stay in
     * lock-step.
     *
     * The `$q` builder may be either an Eloquent Builder (when invoked
     * from `baseAvailableQuery`, where the outer query is a model
     * query) or a Query Builder (when invoked from inside a
     * `whereExists` closure, where Laravel passes the raw query
     * builder). Every method we call below exists on both.
     *
     * @param  Builder|QueryBuilder  $q
     * @return Builder|QueryBuilder
     */
    private function applyAvailableExists(Builder|QueryBuilder $q, ?User $user, string $today, int $offset, int $visibility): Builder|QueryBuilder
    {
        return $q->whereExists(function ($sub) use ($user, $today, $offset, $visibility) {
            $sub->from('course_sections')
                ->whereColumn('course_sections.course_id', 'courses.id')
                ->where(function ($q2) {
                    $q2->whereNull('course_sections.status')
                       ->orWhere('course_sections.status', '!=', 'inactive');
                })
                ->whereNotNull('course_sections.start_date')
                ->whereDate('course_sections.start_date', '>=', $today)
                // App-visibility gate (Figma 332:10708): `open_for_enrollment`
                // cohorts appear immediately; `scheduled` cohorts only appear
                // once they are within `$visibility` days of their start date.
                ->where(function ($q2) use ($today, $visibility) {
                    $q2->where('course_sections.status', 'open_for_enrollment')
                       ->orWhereRaw(
                           'DATE_SUB(course_sections.start_date, INTERVAL ? DAY) <= ?',
                           [$visibility, $today],
                       );
                })
                ->where(function ($q2) use ($today, $offset) {
                    $q2->where(function ($q3) use ($today) {
                        $q3->whereNotNull('course_sections.enrolment_closes_at')
                           ->whereDate('course_sections.enrolment_closes_at', '>=', $today);
                    })->orWhere(function ($q3) use ($today, $offset) {
                        $q3->whereNull('course_sections.enrolment_closes_at')
                           ->whereRaw(
                               'DATE_SUB(course_sections.start_date, INTERVAL ? DAY) >= ?',
                               [$offset, $today],
                           );
                    });
                })
                ->where(function ($q2) {
                    $q2->whereNull('course_sections.capacity')
                       ->orWhereRaw(
                           '(SELECT COUNT(*) FROM users_courses uc WHERE uc.group_id = course_sections.id) < course_sections.capacity',
                       );
                });

            // A guest has no enrolments to exclude; the per-cohort
            // "already joined" filter only applies to an authenticated user.
            if ($user !== null) {
                $sub->whereNotExists(function ($q2) use ($user) {
                    $q2->from('users_courses')
                       ->whereColumn('users_courses.group_id', 'course_sections.id')
                       ->where('users_courses.user_id', $user->id);
                });
            }
        });
    }

}
