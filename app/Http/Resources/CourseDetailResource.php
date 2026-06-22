<?php

namespace App\Http\Resources;

use App\Models\CourseRating;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CourseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Roll the stored `active` flag up against the cohort calendar.
        // The effective status is what we surface to clients ("active"
        // when a cohort is currently running, "upcoming" when one is
        // queued, "inactive" otherwise) so the badge tells the truth
        // without waiting for the nightly sync job to refresh stored
        // columns. See Course::effectiveStatus().
        $effectiveStatus = $this->resource->effectiveStatus();
        $effectiveActive = $effectiveStatus === 'active';

        // Per-course override for "Max per Cohort". Falls back to the
        // platform-wide `default_cohort_size` setting so the Course Settings
        // panel always shows a useful number instead of an em-dash.
        $maxLearners = $this->max_learners;
        if ($maxLearners === null) {
            $maxLearners = (int) (Setting::query()
                ->where('key', 'default_cohort_size')
                ->value('value') ?? 30);
        }

        // The pass percent isn't stored per-course yet — surface the platform
        // default so the certificate row can render "Yes — min 75%".
        $passPercent = (int) (Setting::query()
            ->where('key', 'min_passing_score')
            ->value('value') ?? 0);

        $ratingCount       = (int) ($this->rating_count ?? 0);
        $ratingAverage     = $ratingCount > 0
            ? round((float) ($this->rating_avg ?? 0), 1)
            : 0.0;
        $commentsCount     = (int) ($this->comments_count ?? 0);

        return [
            'id'                 => $this->id,
            'title'              => $this->getTranslation('title', app()->getLocale()),
            'description'        => $this->getTranslation('description', app()->getLocale()),
            'course_type'        => $this->course_type,
            'category'           => $this->whenLoaded('category', fn () => [
                'id'   => $this->category->id,
                'name' => $this->category->getTranslation('name', app()->getLocale()),
            ]),
            'instructors'        => $this->whenLoaded('instructors',
                fn () => $this->instructors->map(fn ($i) => [
                    'id'    => $i->id,
                    'name'  => $i->getTranslation('name', app()->getLocale()),
                    'image' => $i->image ? $i->getFileUrl($i->image) : null,
                ]),
            ),
            'qualification_skills' => $this->whenLoaded('qualificationSkills',
                fn () => $this->qualificationSkills->map(fn ($s) => [
                    'id'   => $s->id,
                    'name' => $s->getTranslation('name', app()->getLocale()),
                ]),
            ),
            // Run sections through the resource so the cohort `status`
            // field reflects the live calendar (scheduled → active →
            // completed) rather than the stale persisted value.
            'sections'           => $this->whenLoaded('sections',
                fn () => CourseSectionResource::collection($this->sections),
            ),
            'exams'              => $this->whenLoaded('exams', fn () => $this->exams->map(fn ($e) => [
                'id'       => $e->id,
                'title'    => $e->getTranslation('title', app()->getLocale()),
                'degree'   => $e->degree,
                'is_final' => (bool) $e->is_final,
            ])),
            'image'              => $this->image ? $this->getFileUrl($this->image) : null,
            'intro_video'        => $this->intro_video,
            'hours'              => $this->hours,
            'max_learners'       => $maxLearners,
            'max_learners_override' => $this->max_learners,
            'language'           => $this->language,
            'level'              => $this->level,
            'price'              => $this->price,
            'currency'           => $this->currency,
            'certificate'              => (bool) $this->certificate,
            'certificate_pass_percent' => $this->certificate ? $passPercent : null,
            'title_for_certificate'    => $this->getTranslation('title_for_certificate', app()->getLocale()),
            'active'             => $effectiveActive,
            'stored_active'      => (bool) $this->active,
            'for_public'         => (bool) $this->for_public,
            'is_evaluate'        => (bool) $this->is_evaluate,
            'outside_materials'  => (bool) $this->outside_materials,
            'allow_attendances'  => (bool) $this->allow_attendances,
            'created_at'         => $this->created_at?->format('Y-m-d'),
            'updated_at'         => $this->updated_at?->format('Y-m-d'),
            'course_type'        => $this->course_type,
            'type'               => $this->course_type,
            // Drives the badge in the Courses table + the Course
            // Settings card. Always derived — never stale.
            'status'             => $effectiveStatus,
            'users_count'        => $this->users_count ?? null,
            'enrolled_count'     => $this->users_count ?? 0,
            'cohorts_count'      => (int) ($this->cohorts_count ?? $this->sessions_count ?? 0),
            'instructor'         => $this->whenLoaded('instructors', function () {
                $first = $this->instructors->first();
                return $first ? [
                    'id'   => $first->id,
                    'name' => $first->getTranslation('name', app()->getLocale()),
                ] : null;
            }),
            'qualifications'     => $this->whenLoaded('qualificationSkills',
                fn () => $this->qualificationSkills->map(fn ($s) => [
                    'id'   => $s->id,
                    'name' => $s->getTranslation('name', app()->getLocale()),
                ]),
            ),

            // Learner-engagement metrics. These power the four KPI cards and
            // the Ratings tab on the course detail screen.
            'rating'              => $ratingAverage,
            'rating_count'        => $ratingCount,
            'comments_count'      => $commentsCount,
            'rating_distribution' => $this->resolveRatingDistribution(),
            'completion_percent'  => $this->resolveCompletionPercent(),
            'reviews'             => $this->resolveReviews(),
        ];
    }

    /**
     * Five-bucket distribution counts ordered from 5★ down to 1★.
     * One aggregated query so the detail page stays cheap even with
     * thousands of ratings.
     *
     * @return array<int, int>
     */
    private function resolveRatingDistribution(): array
    {
        $row = CourseRating::query()
            ->where('course_id', $this->id)
            ->selectRaw(
                'SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS s5,'
                .'SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS s4,'
                .'SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS s3,'
                .'SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS s2,'
                .'SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS s1'
            )
            ->first();

        return $row
            ? [(int) $row->s5, (int) $row->s4, (int) $row->s3, (int) $row->s2, (int) $row->s1]
            : [0, 0, 0, 0, 0];
    }

    /**
     * Latest 20 reviews used by the Ratings tab. We rely on the eager-loaded
     * `ratings` relation (already limited to 20 in the repository) so this
     * doesn't trigger an extra query.
     *
     * @return array<int, array<string, mixed>>
     */
    private function resolveReviews(): array
    {
        if (! $this->relationLoaded('ratings')) {
            return [];
        }

        return $this->ratings->map(fn ($r) => [
            'id'                => $r->id,
            'rating'            => (int) $r->rating,
            'comment'           => $r->comment,
            'user_name'         => $r->user?->name ?? 'Unknown',
            'user_machine_code' => $r->user?->machine_code ?? null,
            'created_at'        => $r->created_at?->toIso8601String(),
        ])->values()->all();
    }

    /**
     * Course-wide completion percent — average across every enrolled user's
     * progress through the course lectures. One aggregate query.
     */
    private function resolveCompletionPercent(): int
    {
        $totalLectures = (int) DB::table('course_lectures')
            ->where('course_id', $this->id)
            ->count();

        if ($totalLectures === 0) {
            return 0;
        }

        $totalUsers = (int) DB::table('users_courses')
            ->where('course_id', $this->id)
            ->count();

        if ($totalUsers === 0) {
            return 0;
        }

        $completed = (int) DB::table('user_lecture_progress')
            ->join('course_lectures', 'course_lectures.id', '=', 'user_lecture_progress.lecture_id')
            ->join('users_courses', function ($join) {
                $join->on('users_courses.user_id', '=', 'user_lecture_progress.user_id')
                     ->whereColumn('users_courses.course_id', 'course_lectures.course_id');
            })
            ->where('course_lectures.course_id', $this->id)
            ->where('user_lecture_progress.completed', true)
            ->count();

        $denominator = $totalLectures * $totalUsers;

        return $denominator > 0
            ? (int) floor(($completed * 100) / $denominator)
            : 0;
    }
}
