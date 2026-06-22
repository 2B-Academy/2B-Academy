<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(
        int     $perPage,
        ?string $search,
        ?int    $categoryId,
        ?bool   $active,
        ?string $courseType,
        ?string $status = null,
    ): LengthAwarePaginator {
        $locale = app()->getLocale();

        return $this->model->newQuery()
            // Only the columns the list actually needs — avoids re-decoding
            // very large `description` JSON blobs on every list call.
            ->select([
                'courses.id',
                'courses.title',
                'courses.course_type',
                'courses.category_id',
                'courses.active',
                'courses.certificate',
                'courses.image',
                'courses.created_at',
                'courses.updated_at',
            ])
            ->with([
                'category:id,name',
                'instructors:id,name',
                // Sections drive `effectiveStatus()` on the resource —
                // eager-load them with just the columns we need so the
                // list endpoint stays one query (no N+1).
                'sections:id,course_id,start_date,end_date,status',
            ])
            ->withCount([
                'users as users_count',
                // "Cohorts" in the admin UI are course_sections, not the
                // individual class meetings (course_sessions). Count the
                // `sections` relation so the column is accurate.
                'sections as cohorts_count',
                'ratings as rating_count',
            ])
            ->withAvg('ratings as rating_avg', 'rating')
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search, $locale) {
                // Translatable columns are stored as JSON. Match BOTH the
                // active locale and English so admins can search either.
                $inner->where("title->{$locale}", 'LIKE', "%{$search}%")
                    ->orWhere('title->en', 'LIKE', "%{$search}%")
                    ->orWhere('title->ar', 'LIKE', "%{$search}%");
            }))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when(!is_null($active), fn ($q) => $q->where('active', $active))
            ->when($courseType, fn ($q) => $q->where('course_type', $courseType))
            ->when($status, fn ($q) => $this->applyStatusFilter($q, $status))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Return all admin tab counts in a single aggregate query so the list
     * page doesn't have to fire one paginated request per status.
     *
     * Status is derived from `course_sections.start_date / end_date`
     * (see Course::effectiveStatus) so the calendar drives the count:
     *   - active   = any cohort `start_date <= today <= end_date` (and not stored `inactive`)
     *   - upcoming = no active cohort, but at least one cohort `today < start_date`
     *   - inactive = anything else (no cohorts, or every cohort completed/inactive)
     *
     * @return array{all: int, active: int, inactive: int, pending: int, upcoming: int}
     */
    public function tabCounts(): array
    {
        // `now()->toDateString()` is a controlled `YYYY-MM-DD` from the
        // server clock — no user input, no injection surface — so we
        // embed it directly instead of juggling 5+ identical `?`
        // bindings across nested CASE/EXISTS expressions (Laravel's
        // selectRaw binding indexing is fragile when the same value
        // appears in multiple correlated subqueries).
        $today = now()->toDateString();

        $hasActiveExpr = "EXISTS (
            SELECT 1 FROM course_sections cs
            WHERE cs.course_id = courses.id
              AND (cs.status IS NULL OR cs.status <> 'inactive')
              AND cs.start_date IS NOT NULL AND cs.end_date IS NOT NULL
              AND cs.start_date <= '{$today}' AND cs.end_date >= '{$today}'
        )";
        $hasScheduledExpr = "EXISTS (
            SELECT 1 FROM course_sections cs
            WHERE cs.course_id = courses.id
              AND (cs.status IS NULL OR cs.status <> 'inactive')
              AND cs.start_date IS NOT NULL
              AND cs.start_date > '{$today}'
        )";

        $row = $this->model->newQuery()
            ->selectRaw("
                COUNT(*) AS all_count,
                SUM(CASE WHEN {$hasActiveExpr} THEN 1 ELSE 0 END) AS active_count,
                SUM(CASE WHEN NOT {$hasActiveExpr} AND {$hasScheduledExpr} THEN 1 ELSE 0 END) AS upcoming_count
            ")
            ->first();

        $all      = (int) ($row->all_count      ?? 0);
        $active   = (int) ($row->active_count   ?? 0);
        $upcoming = (int) ($row->upcoming_count ?? 0);
        $inactive = max(0, $all - $active - $upcoming);

        return [
            'all'      => $all,
            'active'   => $active,
            'inactive' => $inactive,
            'upcoming' => $upcoming,
            // No dedicated workflow column yet — surface zero so the tab
            // contract stays stable until that lands.
            'pending'  => 0,
        ];
    }

    public function allActive(): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->with('category:id,name')
            ->latest()
            ->get();
    }

    public function findWithRelations(int $id): Course
    {
        return $this->model->newQuery()
            ->with([
                'category:id,name',
                'instructors:id,name,image',
                'qualificationSkills:id,name',
                'sections',
                'exams:id,course_id,title,degree,is_final',
                // Latest 20 reviews so the Ratings tab can render without an
                // extra round-trip. Includes machine_code so the reviewer
                // row shows the same `NAS-####` subline as in Figma.
                'ratings' => fn ($q) => $q
                    ->with('user:id,name,machine_code')
                    ->latest()
                    ->limit(20),
            ])
            ->withCount([
                'users as users_count',
                'sessions as sessions_count',
                // Cohorts = course_sections (not the per-cohort meetings).
                'sections as cohorts_count',
                'ratings as rating_count',
                // Only count ratings that actually carry a comment so the
                // "X reviews · Y with comments" header is accurate.
                'ratings as comments_count' => fn ($q) => $q
                    ->whereNotNull('comment')
                    ->where('comment', '!=', ''),
            ])
            ->withAvg('ratings as rating_avg', 'rating')
            ->findOrFail($id);
    }

    public function findWithBasicRelations(int $id): Course
    {
        return $this->model->newQuery()
            ->with([
                'category:id,name',
                'instructors:id,name',
                'qualificationSkills:id,name',
            ])
            ->findOrFail($id);
    }

    public function activePluckedTitles(): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->orderBy('id')
            ->pluck('title', 'id');
    }

    /**
     * Apply the admin tab status filter without leaking the mapping into
     * the controller layer. Status is computed from cohort dates so the
     * filter agrees with what CourseResource emits.
     */
    private function applyStatusFilter($query, string $status)
    {
        $today = now()->toDateString();

        $activeExists = function ($q) use ($today) {
            $q->from('course_sections')
              ->whereColumn('course_sections.course_id', 'courses.id')
              ->where(function ($q2) {
                  $q2->whereNull('course_sections.status')
                     ->orWhere('course_sections.status', '!=', 'inactive');
              })
              ->whereNotNull('course_sections.start_date')
              ->whereNotNull('course_sections.end_date')
              ->whereDate('course_sections.start_date', '<=', $today)
              ->whereDate('course_sections.end_date',   '>=', $today);
        };

        $scheduledExists = function ($q) use ($today) {
            $q->from('course_sections')
              ->whereColumn('course_sections.course_id', 'courses.id')
              ->where(function ($q2) {
                  $q2->whereNull('course_sections.status')
                     ->orWhere('course_sections.status', '!=', 'inactive');
              })
              ->whereNotNull('course_sections.start_date')
              ->whereDate('course_sections.start_date', '>', $today);
        };

        return match ($status) {
            'active'   => $query->whereExists($activeExists),
            'upcoming' => $query
                ->whereNotExists($activeExists)
                ->whereExists($scheduledExists),
            'inactive' => $query
                ->whereNotExists($activeExists)
                ->whereNotExists($scheduledExists),
            'pending'  => $query->whereRaw('0 = 1'), // workflow column not built yet
            default    => $query,
        };
    }
}
