<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Models\PublicNotification;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getStatistics(): array
    {
        $hasLearnerType = Schema::hasColumn('users', 'learner_type');

        $onlineLearnersSql = $hasLearnerType
            ? "(SELECT COUNT(*) FROM users WHERE learner_type = 'online')"
            : '(SELECT COUNT(*) FROM users)';

        $offlineLearnersSql = $hasLearnerType
            ? "(SELECT COUNT(*) FROM users WHERE learner_type = 'offline')"
            : '0';

        // "Active courses" must mean the same thing here as it does on the
        // All Courses list: a course with a cohort whose date window
        // contains today (NOT the raw stored `courses.active` publish
        // flag). We reuse the exact predicate from
        // CourseRepository::tabCounts() so the dashboard card and the
        // list's "Active" tab can never drift. `$today` is a controlled
        // server-clock value (no user input) so it's embedded directly,
        // matching the tabCounts convention.
        $today = now()->toDateString();

        $hasActiveCohortSql = "(SELECT COUNT(*) FROM courses WHERE EXISTS (
            SELECT 1 FROM course_sections cs
            WHERE cs.course_id = courses.id
              AND (cs.status IS NULL OR cs.status <> 'inactive')
              AND cs.start_date IS NOT NULL AND cs.end_date IS NOT NULL
              AND cs.start_date <= '{$today}' AND cs.end_date >= '{$today}'
        ))";

        return (array) DB::selectOne("
            SELECT
                {$hasActiveCohortSql}                                                     AS active_courses,
                (SELECT COUNT(*) FROM courses WHERE active = 0)                          AS awaiting_publish,
                (SELECT COUNT(*) FROM courses)                                            AS courses,
                (SELECT COUNT(*) FROM users)                                              AS users,
                (SELECT COUNT(*) FROM users)                                              AS active_learners,
                {$onlineLearnersSql}                                                      AS active_learners_online,
                {$offlineLearnersSql}                                                     AS active_learners_offline,
                (SELECT COUNT(*) FROM instructors)                                        AS instructors,
                (SELECT COUNT(*) FROM articles WHERE active = 1)                         AS articles,
                (SELECT COUNT(*) FROM course_ratings)                                    AS ratings,
                (SELECT COUNT(*) FROM course_lecture_questions)                          AS lecture_questions,
                (SELECT COUNT(*) FROM course_lecture_questions WHERE answer IS NULL)     AS unanswered_questions,
                (SELECT COUNT(*) FROM user_course_assignments)                           AS user_assignments
        ");
    }

    public function getTopCourses(int $limit): Collection
    {
        return Course::query()
            ->select('id', 'title', 'active')
            ->with([
                'instructors:id,name',
                // Sections power `Course::effectiveStatus()` — eager-load
                // them with just the columns we need so the dashboard
                // top-courses widget stays a single-shot query (no N+1).
                'sections:id,course_id,start_date,end_date,status',
            ])
            ->selectRaw('
                (SELECT COUNT(*) FROM users_courses uc WHERE uc.course_id = courses.id)                                        AS users_count,
                (SELECT COUNT(*) FROM users_courses uc WHERE uc.course_id = courses.id AND uc.updated_at > uc.created_at)      AS completed_count
            ')
            ->orderByDesc('users_count')
            ->limit($limit)
            ->get();
    }

    public function getEnrollmentTrend(int $days = 30): array
    {
        $rows = DB::select("
            SELECT
                d.gen_date AS date,
                COALESCE(e.enrollments, 0) AS enrollments,
                COALESCE(c.completions, 0) AS completions
            FROM (
                SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS gen_date
                FROM (
                    SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                    UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                    UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14
                    UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19
                    UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24
                    UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
                ) nums
                WHERE n < ?
            ) d
            LEFT JOIN (
                SELECT DATE(created_at) AS dt, COUNT(*) AS enrollments
                FROM users_courses
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
            ) e ON e.dt = d.gen_date
            LEFT JOIN (
                SELECT DATE(updated_at) AS dt, COUNT(*) AS completions
                FROM users_courses
                WHERE updated_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND updated_at > created_at
                GROUP BY DATE(updated_at)
            ) c ON c.dt = d.gen_date
            ORDER BY d.gen_date ASC
        ", [$days, $days, $days]);

        return array_map(static fn ($r) => [
            'date'        => $r->date,
            'enrollments' => (int) $r->enrollments,
            'completions' => (int) $r->completions,
        ], $rows);
    }

    /**
     * Range-aware enrollment trend (2026 dashboard).
     *
     *   - week     → 7 daily buckets
     *   - month    → 30 daily buckets
     *   - quarter  → 13 weekly buckets (last 90 days, ISO-week grouped)
     *   - year     → 12 monthly buckets
     *
     * Always returns the full bucket grid (zero-filled), so the chart
     * never has gaps even when no enrollment activity occurred in a
     * given window.
     *
     * @param  'week'|'month'|'quarter'|'year'  $range
     * @return array<int, array{date:string,label:string,enrollments:int,completions:int}>
     */
    public function getEnrollmentTrendByRange(string $range): array
    {
        return match ($range) {
            'week'    => $this->buildDailyTrend(7),
            'quarter' => $this->buildWeeklyTrend(13),
            'year'    => $this->buildMonthlyTrend(12),
            default   => $this->buildDailyTrend(30),
        };
    }

    /**
     * @return array<int, array{date:string,label:string,enrollments:int,completions:int}>
     */
    private function buildDailyTrend(int $days): array
    {
        $rows = $this->getEnrollmentTrend($days);

        return array_map(static function (array $r): array {
            $d = \Illuminate\Support\Carbon::parse($r['date']);
            return [
                'date'        => $d->format('Y-m-d'),
                'label'       => $d->format('d M'),
                'enrollments' => (int) $r['enrollments'],
                'completions' => (int) $r['completions'],
            ];
        }, $rows);
    }

    /**
     * @return array<int, array{date:string,label:string,enrollments:int,completions:int}>
     */
    private function buildWeeklyTrend(int $weeks): array
    {
        $days = $weeks * 7;
        $start = \Illuminate\Support\Carbon::today()->subDays($days - 1)->startOfDay();

        $enrollments = DB::table('users_courses')
            ->selectRaw('YEARWEEK(created_at, 3) AS yw, COUNT(*) AS total')
            ->where('created_at', '>=', $start)
            ->groupBy('yw')
            ->pluck('total', 'yw');

        $completions = DB::table('users_courses')
            ->selectRaw('YEARWEEK(updated_at, 3) AS yw, COUNT(*) AS total')
            ->where('updated_at', '>=', $start)
            ->whereColumn('updated_at', '>', 'created_at')
            ->groupBy('yw')
            ->pluck('total', 'yw');

        $buckets = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = \Illuminate\Support\Carbon::today()->subWeeks($i)->startOfWeek();
            $key = (int) $weekStart->format('oW');
            $buckets[] = [
                'date'        => $weekStart->format('Y-m-d'),
                'label'       => 'W' . $weekStart->isoWeek(),
                'enrollments' => (int) ($enrollments[$key] ?? 0),
                'completions' => (int) ($completions[$key] ?? 0),
            ];
        }

        return $buckets;
    }

    /**
     * @return array<int, array{date:string,label:string,enrollments:int,completions:int}>
     */
    private function buildMonthlyTrend(int $months): array
    {
        $start = \Illuminate\Support\Carbon::today()->subMonthsNoOverflow($months - 1)->startOfMonth();

        $enrollments = DB::table('users_courses')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS total")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $completions = DB::table('users_courses')
            ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m') AS ym, COUNT(*) AS total")
            ->where('updated_at', '>=', $start)
            ->whereColumn('updated_at', '>', 'created_at')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $buckets = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $d   = \Illuminate\Support\Carbon::today()->subMonthsNoOverflow($i)->startOfMonth();
            $key = $d->format('Y-m');
            $buckets[] = [
                'date'        => $d->format('Y-m-d'),
                'label'       => $d->format('M'),
                'enrollments' => (int) ($enrollments[$key] ?? 0),
                'completions' => (int) ($completions[$key] ?? 0),
            ];
        }

        return $buckets;
    }

    /**
     * Recent notifications card on the admin dashboard — sourced directly
     * from the same `public_notifications` table that the notifications
     * drawer + `/api/v1/notifications` endpoint use. Returns the most
     * recently created entries, ordered newest first.
     *
     * The translatable `title` / `body` JSON columns are emitted as full
     * locale objects (`{ ar, en }`) so the frontend can localise them on
     * the fly without an extra request when the UI language is toggled.
     *
     * `created_at` is included raw (ISO-8601) — the frontend renders a
     * relative-time chip from it ("today", "yesterday", ...) so the
     * server doesn't bake an English label into the payload.
     *
     * @return array<int, array{
     *     id: int,
     *     title: array{ar?:string, en?:string},
     *     body: array{ar?:string, en?:string},
     *     for_public: bool,
     *     created_at: string|null,
     * }>
     */
    public function getRecentNotifications(int $limit): array
    {
        return PublicNotification::query()
            ->select(['id', 'title', 'body', 'for_public', 'created_at'])
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(static function (PublicNotification $n): array {
                return [
                    'id'         => (int) $n->id,
                    'title'      => $n->getTranslations('title'),
                    'body'       => $n->getTranslations('body'),
                    'for_public' => (bool) $n->for_public,
                    'created_at' => optional($n->created_at)->toIso8601String(),
                ];
            })
            ->all();
    }
}
