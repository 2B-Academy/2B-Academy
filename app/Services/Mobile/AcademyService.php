<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Enums\Mobile\CourseCtaState;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Repositories\Contracts\Mobile\AcademyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Discovery + enrolment intent for the Academy (S-01 → S-04).
 *
 * Pure orchestration: it asks the repository for the live data, asks
 * `MobileSettings` for every threshold, and resolves view-model
 * details (deadline flags, cohort CTA state, seats progress) so the
 * resources stay declarative.
 */
final class AcademyService
{
    public function __construct(
        private readonly AcademyRepositoryInterface $repository,
        private readonly MobileSettings $settings,
    ) {}

    /**
     * S-01 entry card: count + label.
     *
     * @return array{available_count: int, has_available: bool}
     */
    public function summaryFor(User $user): array
    {
        $count = $this->repository->countAvailableForUser(
            $user,
            now(),
            $this->settings->academyDefaultCloseOffsetDays(),
            $this->settings->academyScheduledVisibilityDays(),
        );

        return [
            'available_count' => $count,
            'has_available'   => $count > 0,
        ];
    }

    /**
     * S-02 scope chips — fixed `All` / `Special` / `General` filters.
     *
     *   - Special → courses tied to the employee's job-title
     *     qualification skills.
     *   - General → every other available course (the complement of
     *     Special), so `all = special + general` always holds.
     *
     * Counts reuse the exact same availability predicate as the list,
     * so the badges always agree with what the list renders.
     *
     * @return Collection<int, array{key: string, label: string, count: int, is_all: bool}>
     */
    public function scopeChipsFor(User $user, string $locale): Collection
    {
        $counts = $this->repository->scopeCounts(
            $user,
            now(),
            $this->settings->academyDefaultCloseOffsetDays(),
            $this->settings->academyScheduledVisibilityDays(),
        );

        return collect([
            ['key' => 'all',     'count' => (int) $counts['all'],     'is_all' => true],
            ['key' => 'special', 'count' => (int) $counts['special'], 'is_all' => false],
            ['key' => 'general', 'count' => (int) $counts['general'], 'is_all' => false],
        ])->map(fn (array $chip) => [
            'key'    => $chip['key'],
            'label'  => __('messages.mobile.scope_' . $chip['key'], [], $locale),
            'count'  => $chip['count'],
            'is_all' => $chip['is_all'],
        ])->values();
    }

    /**
     * S-02 paginated list.
     */
    public function listAvailable(
        User    $user,
        ?int    $categoryId,
        ?string $search,
        ?int    $perPage,
        ?string $scope = null,
    ): LengthAwarePaginator {
        $effectivePerPage = $perPage !== null && $perPage > 0
            ? min($perPage, $this->settings->academyPerPage() * 5)
            : $this->settings->academyPerPage();

        $minChars = $this->settings->academySearchMinChars();
        $cleanSearch = $search !== null && mb_strlen(trim($search)) >= $minChars
            ? trim($search)
            : null;

        return $this->repository->paginateAvailable(
            user: $user,
            now: now(),
            defaultCloseOffsetDays: $this->settings->academyDefaultCloseOffsetDays(),
            scheduledVisibilityDays: $this->settings->academyScheduledVisibilityDays(),
            perPage: $effectivePerPage,
            categoryId: $categoryId,
            search: $cleanSearch,
            scope: $this->normaliseScope($scope),
        );
    }

    /**
     * Whitelist the scope so a stray query string can never reach the
     * repository. Unknown values collapse to `all`.
     */
    private function normaliseScope(?string $scope): string
    {
        return in_array($scope, ['special', 'general'], true) ? $scope : 'all';
    }

    /**
     * S-03 detail.
     */
    public function findDetail(int $courseId): Course
    {
        return $this->repository->findForDetail($courseId);
    }

    /**
     * Resolve the cohort the S-03 detail screen should anchor on for
     * THIS user — either the next joinable cohort, or the one they're
     * already enrolled in if any.
     */
    public function anchorCohortFor(Course $course, User $user): ?CourseSection
    {
        // 1. Already-enrolled cohort wins so the CTA reads "Enrolled ✓".
        $enrolledCohortId = \DB::table('users_courses')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id');

        if (!empty($enrolledCohortId)) {
            return $course->sections->firstWhere('id', (int) $enrolledCohortId)
                ?? CourseSection::query()->find($enrolledCohortId);
        }

        return $this->repository->nextJoinableCohort(
            $course,
            $user,
            now(),
            $this->settings->academyDefaultCloseOffsetDays(),
            $this->settings->academyScheduledVisibilityDays(),
        );
    }

    /**
     * Compute the CTA state for the S-03 sticky button.
     */
    public function resolveCtaState(Course $course, User $user, ?CourseSection $anchorCohort): CourseCtaState
    {
        if ($this->repository->isEnrolledInCourse($user, $course->id)) {
            return CourseCtaState::EnrolledViewLearning;
        }

        if ($anchorCohort === null) {
            return CourseCtaState::Unavailable;
        }

        // Determine whether the deadline has passed.
        $deadline = $this->effectiveDeadline($anchorCohort);
        if ($deadline === null || $deadline->isPast()) {
            return CourseCtaState::GetNotified;
        }

        // Capacity check.
        $capacity = $anchorCohort->capacity;
        if ($capacity !== null) {
            $enrolled = $anchorCohort->enrolled_count ?? \DB::table('users_courses')
                ->where('group_id', $anchorCohort->id)->count();
            if ($enrolled >= $capacity) {
                return CourseCtaState::Unavailable;
            }
        }

        return CourseCtaState::EnrolNow;
    }

    /**
     * Days before its start a `scheduled` cohort becomes app-visible.
     * Exposed so collaborators (e.g. EnrolmentService) resolve the next
     * joinable cohort with the same visibility window as the listing.
     */
    public function scheduledVisibilityDays(): int
    {
        return $this->settings->academyScheduledVisibilityDays();
    }

    /**
     * Effective enrolment deadline = explicit `enrolment_closes_at`
     * if set, otherwise `start_date - mobile_academy.default_close_offset_days`.
     */
    public function effectiveDeadline(CourseSection $cohort): ?Carbon
    {
        if ($cohort->enrolment_closes_at !== null) {
            return Carbon::parse($cohort->enrolment_closes_at)->endOfDay();
        }

        if ($cohort->start_date === null) {
            return null;
        }

        $offset = $this->settings->academyDefaultCloseOffsetDays();

        return Carbon::parse($cohort->start_date)
            ->subDays($offset)
            ->endOfDay();
    }

    /**
     * Days remaining until the deadline (negative if past).
     */
    public function daysUntilDeadline(CourseSection $cohort, Carbon $now): ?int
    {
        $deadline = $this->effectiveDeadline($cohort);
        if ($deadline === null) {
            return null;
        }

        return (int) $now->startOfDay()->diffInDays($deadline->startOfDay(), false);
    }

    /**
     * Classify a deadline against the platform warning thresholds.
     * Returns one of `none|warning|critical|closed`.
     */
    public function deadlineSeverity(CourseSection $cohort, Carbon $now): string
    {
        $days = $this->daysUntilDeadline($cohort, $now);

        if ($days === null) {
            return 'none';
        }
        if ($days < 0) {
            return 'closed';
        }
        if ($days <= $this->settings->academyDeadlineCriticalDays()) {
            return 'critical';
        }
        if ($days <= $this->settings->academyDeadlineWarningDays()) {
            return 'warning';
        }
        return 'none';
    }
}
