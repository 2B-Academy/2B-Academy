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

    /**
     * Recent admin-dashboard notifications card. Returns the most recent
     * persisted `public_notifications` rows with translatable `title` and
     * `body` JSON columns kept intact (so the frontend can localise them
     * without a re-fetch).
     *
     * @return array<int, array{
     *     id: int,
     *     title: array{ar?:string, en?:string},
     *     body: array{ar?:string, en?:string},
     *     for_public: bool,
     *     created_at: string|null,
     * }>
     */
    public function getRecentNotifications(int $limit): array;
}
