<?php

namespace App\Services\Admin;

use App\Models\ReportExportLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service backing the 2026 Reports redesign.
 *
 * This service is intentionally NEW and additive — the existing
 * App\Http\Controllers\apis\ReportController and its routes
 * (routes/apis/reports.php) remain untouched.
 *
 * Responsibilities:
 *   - Provide the dataset for each of the 6 report cards.
 *   - Provide the live "Compliance by Job Title" preview table.
 *   - Track last-exported timestamps via report_export_logs.
 */
class AdminReportService
{
    /** Canonical list of report types exposed by the admin reports surface. */
    public const TYPES = [
        'compliance-by-job-title',
        'individual-compliance',
        'attendance',
        'completion',
        'scores',
        'certificate-status',
    ];

    /* ------------------------------------------------------------------ *
     |  SUMMARY (cards)                                                   |
     * ------------------------------------------------------------------ */

    /**
     * Human-readable label + description per card.
     *
     * @return array<string,array{label:string,description:string,icon:string}>
     */
    public static function catalog(): array
    {
        return [
            'compliance-by-job-title' => [
                'label'       => 'Compliance by Job Title',
                'description' => 'Qualification completion % per job title across the organisation',
                'icon'        => 'shield-check',
            ],
            'individual-compliance' => [
                'label'       => 'Individual Compliance',
                'description' => "Each employee's qualification status: complete · in progress · not started",
                'icon'        => 'user',
            ],
            'attendance' => [
                'label'       => 'Attendance Report',
                'description' => 'Session attendance per course, cohort, and learner',
                'icon'        => 'calendar-check',
            ],
            'completion' => [
                'label'       => 'Completion Report',
                'description' => 'Course completion status per cohort and learner with dates',
                'icon'        => 'chart',
            ],
            'scores' => [
                'label'       => 'Scores Report',
                'description' => 'Quiz and assignment scores per learner and course',
                'icon'        => 'chart-bar',
            ],
            'certificate-status' => [
                'label'       => 'Certificate Status',
                'description' => 'All earned certificates with issue dates',
                'icon'        => 'badge',
            ],
        ];
    }

    /**
     * Cards payload for the Reports overview.
     *
     * @return array<int,array{
     *   key:string, label:string, description:string, icon:string,
     *   last_generated_at:string|null
     * }>
     */
    public function summary(): array
    {
        $lastByType = ReportExportLog::query()
            ->select('report_type', DB::raw('MAX(exported_at) AS last_exported_at'))
            ->groupBy('report_type')
            ->pluck('last_exported_at', 'report_type');

        $catalog = self::catalog();
        $cards   = [];

        foreach (self::TYPES as $type) {
            $meta = $catalog[$type];
            $cards[] = [
                'key'               => $type,
                'label'             => $meta['label'],
                'description'       => $meta['description'],
                'icon'              => $meta['icon'],
                'last_generated_at' => $lastByType[$type] ?? null,
            ];
        }

        return $cards;
    }

    /* ------------------------------------------------------------------ *
     |  COMPLIANCE PREVIEW (live table)                                   |
     * ------------------------------------------------------------------ */

    /**
     * Grouped compliance preview shown beneath the cards in Figma.
     *
     * Columns: Role · Department · Learners · Qualified · Compliance %
     *
     * "Qualified" is defined as a learner who is enrolled in at least one
     * course (heuristic that maps well to the existing data model while
     * staying entirely additive). Department is the most common
     * `department_name` for users sharing the role.
     *
     * @return array<int,array{
     *   role:string,
     *   department:string|null,
     *   learners:int,
     *   qualified:int,
     *   compliance_pct:int
     * }>
     */
    public function compliancePreview(int $limit = 25): array
    {
        // Pre-compute the role bucket in a derived subquery so the outer
        // aggregation can group on a single plain column. The 2026
        // redesign dropped `users.job_title`, so the bucket key now
        // comes from the Spatie `model_has_roles` pivot — falling back
        // to "Learner" for users without an admin-guard role.
        $locale = app()->getLocale();
        $userClass = User::class;

        $usersSub = DB::table('users')
            ->leftJoin('model_has_roles AS mhr', function ($j) use ($userClass) {
                $j->on('mhr.model_id', '=', 'users.id')
                  ->where('mhr.model_type', '=', $userClass);
            })
            ->leftJoin('roles', function ($j) {
                $j->on('roles.id', '=', 'mhr.role_id')
                  ->where('roles.guard_name', '=', 'admin');
            })
            ->select(
                'users.id AS user_id',
                'users.department_name AS department_name',
                DB::raw(sprintf(
                    'COALESCE(NULLIF(roles.%s, ""), NULLIF(roles.name_en, ""), roles.name, "Learner") AS role_key',
                    $locale === 'ar' ? 'name_ar' : 'name_en',
                )),
            );

        $rows = DB::query()
            ->fromSub($usersSub, 'u')
            ->leftJoin('users_courses AS uc', 'uc.user_id', '=', 'u.user_id')
            ->select(
                'u.role_key AS role',
                DB::raw('COUNT(DISTINCT u.user_id) AS learners'),
                DB::raw('COUNT(DISTINCT CASE WHEN uc.user_id IS NOT NULL THEN u.user_id END) AS qualified'),
            )
            ->groupBy('u.role_key')
            ->orderByDesc('learners')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        // Most common department per role — same derived-subquery trick
        // keeps this safe under strict GROUP BY.
        $deptByRole = DB::query()
            ->fromSub($usersSub, 'u')
            ->select(
                'u.role_key AS role',
                'u.department_name',
                DB::raw('COUNT(*) AS occurrences'),
            )
            ->whereNotNull('u.department_name')
            ->groupBy('u.role_key', 'u.department_name')
            ->orderByDesc('occurrences')
            ->get()
            ->groupBy('role')
            ->map(fn ($items) => optional($items->first())->department_name);

        return $rows->map(function ($r) use ($deptByRole) {
            $learners  = (int) $r->learners;
            $qualified = (int) $r->qualified;
            $pct       = $learners > 0 ? (int) round(($qualified / $learners) * 100) : 0;

            return [
                'role'           => (string) $r->role,
                'department'     => $deptByRole[$r->role] ?? null,
                'learners'       => $learners,
                'qualified'      => $qualified,
                'compliance_pct' => $pct,
            ];
        })->all();
    }

    /* ------------------------------------------------------------------ *
     |  DATASETS                                                          |
     * ------------------------------------------------------------------ */

    /**
     * Build the full tabular dataset for a given report type. The first
     * element of the returned tuple is the column headings, the second is
     * the row data.
     *
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     *
     * @throws \InvalidArgumentException When the report type is unknown.
     */
    public function dataset(string $type): array
    {
        return match ($type) {
            'compliance-by-job-title' => $this->buildComplianceByJobTitle(),
            'individual-compliance'   => $this->buildIndividualCompliance(),
            'attendance'              => $this->buildAttendance(),
            'completion'              => $this->buildCompletion(),
            'scores'                  => $this->buildScores(),
            'certificate-status'      => $this->buildCertificateStatus(),
            default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
        };
    }

    /* ------------------------------------------------------------------ *
     |  LOG                                                               |
     * ------------------------------------------------------------------ */

    public function logExport(string $type, string $format, ?int $adminId): void
    {
        ReportExportLog::query()->create([
            'report_type'          => $type,
            'format'               => $format,
            'exported_by_admin_id' => $adminId,
            'exported_at'          => Carbon::now(),
        ]);
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL — Dataset builders                                       |
     * ------------------------------------------------------------------ */

    /**
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     */
    private function buildComplianceByJobTitle(): array
    {
        $rows = $this->compliancePreview(limit: 500);

        return [
            ['Role', 'Department', 'Learners', 'Qualified', 'Compliance %'],
            array_map(static fn ($r) => [
                $r['role'],
                $r['department'] ?? '',
                $r['learners'],
                $r['qualified'],
                $r['compliance_pct'] . '%',
            ], $rows),
        ];
    }

    /**
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     */
    private function buildIndividualCompliance(): array
    {
        $locale    = app()->getLocale();
        $userClass = User::class;
        $roleNameExpr = $locale === 'ar' ? 'roles.name_ar' : 'roles.name_en';

        $rows = DB::table('users')
            ->leftJoin('users_courses AS uc', 'uc.user_id', '=', 'users.id')
            ->leftJoin('model_has_roles AS mhr', function ($j) use ($userClass) {
                $j->on('mhr.model_id', '=', 'users.id')
                  ->where('mhr.model_type', '=', $userClass);
            })
            ->leftJoin('roles', function ($j) {
                $j->on('roles.id', '=', 'mhr.role_id')
                  ->where('roles.guard_name', '=', 'admin');
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw("COALESCE(NULLIF({$roleNameExpr}, ''), NULLIF(roles.name_en, ''), roles.name, 'Learner') AS role_label"),
                'users.department_name',
                DB::raw('COUNT(uc.id) AS enrolled_courses'),
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'role_label', 'users.department_name')
            ->orderBy('users.name')
            ->get();

        return [
            ['User ID', 'Name', 'Email', 'Role', 'Department', 'Enrolled Courses', 'Status'],
            $rows->map(static function ($r) {
                $enrolled = (int) $r->enrolled_courses;
                $status   = $enrolled === 0 ? 'Not started' : ($enrolled >= 1 ? 'In progress' : 'Complete');

                return [
                    $r->id,
                    $r->name,
                    $r->email,
                    $r->role_label ?? '',
                    $r->department_name ?? '',
                    $enrolled,
                    $status,
                ];
            })->all(),
        ];
    }

    /**
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     */
    private function buildAttendance(): array
    {
        $rows = DB::table('attendances')
            ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
            ->select(
                'users.name AS learner',
                'users.email AS email',
                'attendances.course_name',
                'attendances.user_department',
                'attendances.attendance_hours',
                'attendances.course_hours',
                'attendances.created_at',
            )
            ->orderByDesc('attendances.created_at')
            ->get();

        return [
            ['Learner', 'Email', 'Course', 'Department', 'Attendance Hours', 'Course Hours', 'Date'],
            $rows->map(static fn ($r) => [
                $r->learner          ?? '',
                $r->email            ?? '',
                $r->course_name      ?? '',
                $r->user_department  ?? '',
                $r->attendance_hours ?? 0,
                $r->course_hours     ?? 0,
                $r->created_at       ? Carbon::parse($r->created_at)->format('Y-m-d') : '',
            ])->all(),
        ];
    }

    /**
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     */
    private function buildCompletion(): array
    {
        $rows = DB::table('users_courses')
            ->leftJoin('users',   'users.id',   '=', 'users_courses.user_id')
            ->leftJoin('courses', 'courses.id', '=', 'users_courses.course_id')
            ->select(
                'users.name  AS learner',
                'users.email AS email',
                'courses.id  AS course_id',
                'courses.title AS course_title',
                'users_courses.created_at AS enrolled_at',
                'users_courses.group_id   AS cohort_id',
            )
            ->orderByDesc('users_courses.created_at')
            ->get();

        return [
            ['Learner', 'Email', 'Course ID', 'Course', 'Cohort', 'Enrolled At'],
            $rows->map(static fn ($r) => [
                $r->learner      ?? '',
                $r->email        ?? '',
                $r->course_id,
                $r->course_title ?? '',
                $r->cohort_id    ?? '',
                $r->enrolled_at  ? Carbon::parse($r->enrolled_at)->format('Y-m-d') : '',
            ])->all(),
        ];
    }

    /**
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     */
    private function buildScores(): array
    {
        $rows = DB::table('user_exams')
            ->leftJoin('users',        'users.id',        '=', 'user_exams.user_id')
            ->leftJoin('courses',      'courses.id',      '=', 'user_exams.course_id')
            ->leftJoin('course_exams', 'course_exams.id', '=', 'user_exams.exam_id')
            ->select(
                'users.name   AS learner',
                'users.email  AS email',
                'courses.title AS course_title',
                'course_exams.title AS exam_title',
                'user_exams.user_degree',
                'user_exams.status',
                'user_exams.created_at',
            )
            ->orderByDesc('user_exams.created_at')
            ->get();

        return [
            ['Learner', 'Email', 'Course', 'Exam', 'Score', 'Status', 'Date'],
            $rows->map(static fn ($r) => [
                $r->learner      ?? '',
                $r->email        ?? '',
                $r->course_title ?? '',
                $r->exam_title   ?? '',
                $r->user_degree  ?? 0,
                $r->status       ?? '',
                $r->created_at   ? Carbon::parse($r->created_at)->format('Y-m-d') : '',
            ])->all(),
        ];
    }

    /**
     * @return array{0: list<string>, 1: list<list<string|int|null>>}
     */
    private function buildCertificateStatus(): array
    {
        // Earned certificate = enrolled in a course (the existing platform
        // issues a certificate-of-enrollment once a learner is added). This
        // mirrors the legacy ReportController query.
        $rows = DB::table('users_courses')
            ->leftJoin('users',   'users.id',   '=', 'users_courses.user_id')
            ->leftJoin('courses', 'courses.id', '=', 'users_courses.course_id')
            ->select(
                'users.name    AS learner',
                'users.email   AS email',
                'courses.title AS course_title',
                'users_courses.created_at AS issued_at',
            )
            ->orderByDesc('users_courses.created_at')
            ->get();

        return [
            ['Learner', 'Email', 'Course', 'Issued At'],
            $rows->map(static fn ($r) => [
                $r->learner      ?? '',
                $r->email        ?? '',
                $r->course_title ?? '',
                $r->issued_at    ? Carbon::parse($r->issued_at)->format('Y-m-d') : '',
            ])->all(),
        ];
    }
}
