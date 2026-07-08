<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function getStatistics(): array;
    public function getTopCourses(int $limit): Collection;
    public function getEnrollmentTrend(int $days): array;

    /**
     * Range-aware enrollment trend used by the 2026 dashboard chart.
     *
     * @param  'week'|'month'|'quarter'|'year'  $range
     * @return array<int, array{date: string, label: string, enrollments: int, completions: int}>
     */
    public function getEnrollmentTrendByRange(string $range): array;
}
