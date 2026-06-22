<?php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $repo
    ) {}

    /**
     * @param  'week'|'month'|'quarter'|'year'|null  $range
     */
    public function getSummary(?string $range = null): array
    {
        $statistics      = $this->repo->getStatistics();
        $resolvedRange   = $this->resolveRange($range);
        $enrollmentTrend = $this->repo->getEnrollmentTrendByRange($resolvedRange);
        $locale     = app()->getLocale();
        $topCourses = $this->repo->getTopCourses(10)
            ->map(function ($c) use ($locale) {
                $enrolled  = (int) ($c->users_count ?? 0);
                $completed = (int) ($c->completed_count ?? 0);
                $percent   = $enrolled > 0 ? (int) round($completed * 100 / $enrolled) : 0;

                $instructorName = optional($c->instructors->first())
                    ?->getTranslation('name', $locale);

                // Derive status from the cohort calendar instead of the
                // stored `active` flag. Mirrors what CourseResource and
                // CourseDetailResource emit so the dashboard agrees with
                // the Courses list / detail badges. The persisted
                // `active` column may still lag until the daily
                // `cohorts:sync-statuses` cron runs — this keeps the
                // dashboard live in between.
                return [
                    'id'                 => $c->id,
                    'title'              => $c->getTranslation('title', $locale),
                    'instructor'         => $instructorName ?: null,
                    'users_count'        => $enrolled,
                    'completion_percent' => $percent,
                    'status'             => $c->effectiveStatus(),
                ];
            });

        return [
            'statistics'       => $statistics,
            'top_courses'      => $topCourses,
            'enrollment_trend' => $enrollmentTrend,
            'trend_range'      => $resolvedRange,
            'notifications'    => $this->repo->getRecentNotifications(8),
        ];
    }

    private function resolveRange(?string $range): string
    {
        return match ($range) {
            'week', 'quarter', 'year' => $range,
            default                   => 'month',
        };
    }
}
