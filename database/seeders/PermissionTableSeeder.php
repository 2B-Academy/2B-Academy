<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the spatie/permission `permissions` table.
 *
 * Mirrors the exact ID + table_name + name combinations exported
 * from production so role_has_permissions / model_has_permissions
 * pivots can be replayed without re-mapping IDs.
 */
class PermissionTableSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $now = Carbon::parse('2025-07-06 18:53:02');

        $rows = [
            [1, 'users', 'users-index', '2025-07-06 18:53:02'],
            [2, 'users', 'users-show', '2025-07-06 18:53:02'],
            [3, 'abouts', 'abouts-edit', '2025-07-06 18:53:02'],
            [4, 'blogs', 'blogs-index', '2025-07-06 18:53:02'],
            [5, 'blogs', 'blogs-create', '2025-07-06 18:53:02'],
            [6, 'blogs', 'blogs-edit', '2025-07-06 18:53:02'],
            [7, 'blogs', 'blogs-delete', '2025-07-06 18:53:02'],
            [8, 'facts', 'facts-index', '2025-07-06 18:53:02'],
            [9, 'facts', 'facts-create', '2025-07-06 18:53:02'],
            [10, 'facts', 'facts-edit', '2025-07-06 18:53:02'],
            [11, 'facts', 'facts-delete', '2025-07-06 18:53:02'],
            [12, 'partners', 'partners-index', '2025-07-06 18:53:02'],
            [13, 'partners', 'partners-create', '2025-07-06 18:53:02'],
            [14, 'partners', 'partners-edit', '2025-07-06 18:53:02'],
            [15, 'partners', 'partners-delete', '2025-07-06 18:53:02'],
            [16, 'testimonials', 'testimonials-index', '2025-07-06 18:53:02'],
            [17, 'testimonials', 'testimonials-create', '2025-07-06 18:53:02'],
            [18, 'testimonials', 'testimonials-edit', '2025-07-06 18:53:02'],
            [19, 'testimonials', 'testimonials-delete', '2025-07-06 18:53:02'],
            [20, 'careers', 'careers-index', '2025-07-06 18:53:02'],
            [21, 'careers', 'careers-create', '2025-07-06 18:53:02'],
            [22, 'careers', 'careers-edit', '2025-07-06 18:53:02'],
            [23, 'careers', 'careers-delete', '2025-07-06 18:53:02'],
            [24, 'contact_form', 'contact_form-index', '2025-07-06 18:53:02'],
            [25, 'contact_form', 'contact_form-show', '2025-07-06 18:53:02'],
            [26, 'contact_form', 'contact_form-delete', '2025-07-06 18:53:02'],
            [27, 'settings', 'settings-edit', '2025-07-06 18:53:02'],
            [28, 'seo', 'seo-edit', '2025-07-06 18:53:02'],
            [29, 'admins', 'admins-index', '2025-07-06 18:53:02'],
            [30, 'admins', 'admins-create', '2025-07-06 18:53:02'],
            [31, 'admins', 'admins-edit', '2025-07-06 18:53:02'],
            [32, 'admins', 'admins-delete', '2025-07-06 18:53:02'],
            [33, 'roles', 'roles-index', '2025-07-06 18:53:02'],
            [34, 'roles', 'roles-create', '2025-07-06 18:53:02'],
            [35, 'roles', 'roles-edit', '2025-07-06 18:53:02'],
            [36, 'roles', 'roles-delete', '2025-07-06 18:53:02'],
            [37, 'categories', 'categories-index', '2025-07-29 13:35:37'],
            [38, 'categories', 'categories-create', '2025-07-29 13:35:37'],
            [39, 'categories', 'categories-edit', '2025-07-29 13:35:37'],
            [40, 'categories', 'categories-delete', '2025-07-29 13:35:37'],
            [41, 'courses', 'courses-index', '2025-07-30 12:02:29'],
            [42, 'courses', 'courses-create', '2025-07-30 12:02:29'],
            [43, 'courses', 'courses-edit', '2025-07-30 12:02:29'],
            [44, 'courses', 'courses-delete', '2025-07-30 12:02:29'],
            [45, 'courses-sections', 'courses-sections-index', '2025-07-30 15:03:36'],
            [46, 'courses-resources', 'courses-resources-index', '2025-08-06 10:58:39'],
            [47, 'instructors', 'instructors-index', '2025-08-06 12:07:36'],
            [48, 'instructors', 'instructors-create', '2025-08-06 12:07:36'],
            [49, 'instructors', 'instructors-edit', '2025-08-06 12:07:36'],
            [50, 'instructors', 'instructors-delete', '2025-08-06 12:07:36'],
            [51, 'courses-lectures', 'courses-lectures-index', '2025-08-06 14:43:12'],
            [52, 'courses-lectures', 'courses-lectures-create', '2025-08-06 14:43:12'],
            [53, 'courses-lectures', 'courses-lectures-edit', '2025-08-06 14:43:12'],
            [54, 'courses-lectures', 'courses-lectures-delete', '2025-08-06 14:43:12'],
            [55, 'courses-exams', 'courses-exams-index', '2025-08-06 17:46:51'],
            [56, 'courses-exams', 'courses-exams-create', '2025-08-06 17:46:51'],
            [57, 'courses-exams', 'courses-exams-edit', '2025-08-06 17:46:51'],
            [58, 'courses-exams', 'courses-exams-delete', '2025-08-06 17:46:51'],
            [59, 'users-courses', 'users-courses-index', '2025-08-16 13:30:11'],
            [60, 'users-courses', 'users-courses-create', '2025-08-16 13:30:11'],
            [61, 'users-courses', 'users-courses-edit', '2025-08-16 13:30:11'],
            [62, 'users-courses', 'users-courses-delete', '2025-08-16 13:30:11'],
            [63, 'users-courses-progress', 'users-courses-progress-index', '2025-09-01 11:41:15'],
            [64, 'users-courses-rating', 'users-courses-rating-index', '2025-09-01 14:23:02'],
            [65, 'users-courses-rating', 'users-courses-rating-delete', '2025-09-01 14:23:02'],
            [66, 'users-lectures-questions', 'users-lectures-questions-index', '2025-09-01 14:23:02'],
            [67, 'users-lectures-questions', 'users-lectures-questions-edit', '2025-09-01 14:23:02'],
            [68, 'users-lectures-questions', 'users-lectures-questions-delete', '2025-09-01 14:23:02'],
            [69, 'courses-assignments', 'courses-assignments-index', '2025-09-02 12:42:28'],
            [70, 'users-courses-assignments', 'users-courses-assignments-index', '2025-09-02 16:26:18'],
            [71, 'users-courses-assignments', 'users-courses-assignments-delete', '2025-09-02 16:26:18'],
            [72, 'courses-sessions', 'courses-sessions-index', '2025-10-05 14:48:50'],
            [73, 'courses-sessions', 'courses-sessions-create', '2025-10-05 14:48:50'],
            [74, 'courses-sessions', 'courses-sessions-edit', '2025-10-05 14:48:50'],
            [75, 'courses-sessions', 'courses-sessions-delete', '2025-10-05 14:48:50'],
            [76, 'users-courses-offline', 'users-courses-offline-index', '2025-10-05 18:45:44'],
            [77, 'users-courses-offline', 'users-courses-offline-create', '2025-10-05 18:45:44'],
            [78, 'users-courses-offline', 'users-courses-offline-edit', '2025-10-05 18:45:44'],
            [79, 'users-courses-offline', 'users-courses-offline-delete', '2025-10-05 18:45:44'],
            [80, 'videos', 'videos-index', '2025-10-26 11:42:23'],
            [81, 'videos', 'videos-create', '2025-10-26 11:42:23'],
            [82, 'forms', 'forms-index', '2025-11-10 17:38:27'],
            [83, 'forms', 'forms-create', '2025-11-10 17:38:27'],
            [84, 'forms', 'forms-edit', '2025-11-10 17:38:27'],
            [85, 'forms', 'forms-delete', '2025-11-10 17:38:27'],
            [86, 'users-certificates', 'users-certificates-index', '2025-11-13 19:01:58'],
            [87, 'public_notifications', 'public_notifications-index', '2025-12-22 14:46:00'],
            [88, 'public_notifications', 'public_notifications-create', '2025-12-22 14:46:00'],
            [89, 'evaluation-categories', 'evaluation-categories-index', '2026-01-31 11:42:02'],
            [90, 'evaluation-categories', 'evaluation-categories-create', '2026-01-31 11:42:02'],
            [91, 'evaluation-categories', 'evaluation-categories-edit', '2026-01-31 11:42:02'],
            [92, 'evaluation-categories', 'evaluation-categories-delete', '2026-01-31 11:42:02'],
            [93, 'evaluations', 'evaluations-index', '2026-01-31 11:42:02'],
            [94, 'evaluations', 'evaluations-create', '2026-01-31 11:42:02'],
            [95, 'evaluations', 'evaluations-edit', '2026-01-31 11:42:02'],
            [96, 'evaluations', 'evaluations-delete', '2026-01-31 11:42:02'],
            [97, 'evaluations-reports', 'evaluations-reports-index', '2026-02-03 13:54:54'],
            [98, 'attendances', 'attendances-index', '2026-02-04 10:11:51'],
            [99, 'qualification-skills', 'qualification-skills-index', '2026-05-19 14:00:00'],
            [100, 'qualification-skills', 'qualification-skills-create', '2026-05-19 14:00:00'],
            [101, 'qualification-skills', 'qualification-skills-edit', '2026-05-19 14:00:00'],
            [102, 'qualification-skills', 'qualification-skills-delete', '2026-05-19 14:00:00'],
        ];

        $payload = array_map(function ($row) {
            [$id, $table, $name, $stamp] = $row;
            $ts = Carbon::parse($stamp);

            return [
                'id' => $id,
                'table_name' => $table,
                'name' => $name,
                'guard_name' => 'admin',
                'created_at' => $ts,
                'updated_at' => $ts,
            ];
        }, $rows);

        $this->schemaAwareUpsert('permissions', $payload, ['id'], ['table_name', 'name', 'guard_name', 'updated_at']);

        unset($now);
    }
}
