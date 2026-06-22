<?php

namespace App\Services\Admin;

use App\Models\Course;
use App\Models\CourseRating;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Service backing the rich ratings overview defined by the 2026 Figma redesign.
 *
 * NOTE: This service is intentionally NEW and additive — the existing
 * App\Services\CourseRatingService (and its repository) remain untouched so
 * the legacy learner/admin endpoints in routes/apis/ratings.php continue
 * working without behaviour changes.
 */
class AdminRatingService
{
    /* ------------------------------------------------------------------ *
     |  PAGINATED LIST                                                    |
     * ------------------------------------------------------------------ */

    /**
     * Paginate ratings for the admin overview.
     *
     * @param  array<int,int>|null  $instructorIds
     * @param  array<int,int>|null  $learnerIds
     * @param  array<int,int>|null  $courseIds
     */
    public function paginate(
        ?array $instructorIds,
        ?array $learnerIds,
        ?array $courseIds,
        ?string $search,
        ?int $minRating,
        ?int $maxRating,
        int $perPage = 20
    ): LengthAwarePaginator {
        return CourseRating::query()
            ->with([
                'user:id,name,machine_code',
                'course:id,title',
                'course.instructors:id,name',
            ])
            ->when(!empty($courseIds), fn ($q) => $q->whereIn('course_id', $courseIds))
            ->when(!empty($learnerIds), fn ($q) => $q->whereIn('user_id', $learnerIds))
            ->when(!empty($instructorIds), fn ($q) => $q->whereHas(
                'course.instructors',
                fn ($inner) => $inner->whereIn('instructors.id', $instructorIds),
            ))
            ->when($minRating !== null, fn ($q) => $q->where('rating', '>=', $minRating))
            ->when($maxRating !== null, fn ($q) => $q->where('rating', '<=', $maxRating))
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                      ->orWhere('comment', 'like', "%{$search}%");
            }))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /* ------------------------------------------------------------------ *
     |  SUMMARY                                                           |
     * ------------------------------------------------------------------ */

    /**
     * Build the four KPI cards shown at the top of the page:
     *   Total Ratings · Average Score · 5-Star Reviews · Low Ratings (≤2★)
     *
     * @return array{
     *   total_ratings: int,
     *   average_score: float,
     *   five_star_count: int,
     *   low_count: int
     * }
     */
    public function summary(): array
    {
        $row = CourseRating::query()
            ->selectRaw('COUNT(*) AS total_ratings')
            ->selectRaw('COALESCE(ROUND(AVG(rating), 1), 0) AS average_score')
            ->selectRaw('SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS five_star_count')
            ->selectRaw('SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) AS low_count')
            ->first();

        return [
            'total_ratings'   => (int)   ($row->total_ratings   ?? 0),
            'average_score'   => (float) ($row->average_score   ?? 0),
            'five_star_count' => (int)   ($row->five_star_count ?? 0),
            'low_count'       => (int)   ($row->low_count       ?? 0),
        ];
    }

    /* ------------------------------------------------------------------ *
     |  FILTER OPTIONS                                                    |
     * ------------------------------------------------------------------ */

    /**
     * Lookup payload for the Instructors / Learners / Courses filter modals.
     * Only entities that have at least one rating are returned so the lists
     * stay relevant to what the admin can actually filter against.
     *
     * @return array{
     *   instructors: array<int,array{id:int,name:string|null}>,
     *   learners:    array<int,array{id:int,name:string|null,machine_code:string|null}>,
     *   courses:     array<int,array{id:int,title:string|null}>
     * }
     */
    public function filterOptions(): array
    {
        $courseIds  = CourseRating::query()->distinct()->pluck('course_id');
        $learnerIds = CourseRating::query()->distinct()->pluck('user_id');

        $courses = Course::query()
            ->whereIn('id', $courseIds)
            ->orderBy('title')
            ->get(['id', 'title']);

        $learners = User::query()
            ->whereIn('id', $learnerIds)
            ->orderBy('name')
            ->get(['id', 'name', 'machine_code']);

        $instructorIds = DB::table('courses_instructors')
            ->whereIn('course_id', $courseIds)
            ->distinct()
            ->pluck('instructor_id');

        $instructors = Instructor::query()
            ->whereIn('id', $instructorIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $locale = app()->getLocale();

        return [
            'instructors' => $instructors->map(fn ($i) => [
                'id'   => $i->id,
                'name' => $this->translateField($i, 'name', $locale),
            ])->values()->all(),
            'learners' => $learners->map(fn ($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'machine_code' => $u->machine_code ?? null,
            ])->values()->all(),
            'courses' => $courses->map(fn ($c) => [
                'id'    => $c->id,
                'title' => $this->translateField($c, 'title', $locale),
            ])->values()->all(),
        ];
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL HELPERS                                                  |
     * ------------------------------------------------------------------ */

    private function translateField(object $model, string $field, string $locale): ?string
    {
        if (method_exists($model, 'getTranslation')) {
            try {
                return $model->getTranslation($field, $locale);
            } catch (\Throwable) {
                // fall through
            }
        }

        return $model->{$field} ?? null;
    }
}
