<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            ['id' => 13, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => 'الحسابات',                       'course_category_id' => 3, 'course_category_name' => 'قسم التسويق',     'course_id' => 6, 'course_name' => 'الكورس الأول',  'course_hours' => 20,  'section_id' => 20, 'attendance_hours' => 6.67, 'is_manual' => 0, 'created_at' => '2025-12-31 23:00:01', 'updated_at' => '2026-02-04 23:00:01'],
            ['id' => 14, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => 'الحسابات',                       'course_category_id' => 3, 'course_category_name' => 'قسم التسويق',     'course_id' => 6, 'course_name' => 'الكورس الأول',  'course_hours' => 20,  'section_id' => 20, 'attendance_hours' => 6.67, 'is_manual' => 0, 'created_at' => '2026-02-11 23:00:23', 'updated_at' => '2026-02-04 23:00:23'],
            ['id' => 15, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => 'الحسابات',                       'course_category_id' => 3, 'course_category_name' => 'قسم التسويق',     'course_id' => 6, 'course_name' => 'الكورس الأول',  'course_hours' => 20,  'section_id' => 20, 'attendance_hours' => 6.67, 'is_manual' => 0, 'created_at' => '2026-02-14 23:00:30', 'updated_at' => '2026-02-04 23:00:30'],
            ['id' => 16, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => 'الحسابات',                       'course_category_id' => 4, 'course_category_name' => 'قسم البرمجة',     'course_id' => 7, 'course_name' => 'الكورس الثاني', 'course_hours' => 20,  'section_id' => 23, 'attendance_hours' => 20,   'is_manual' => 0, 'created_at' => '2026-02-04 23:00:39', 'updated_at' => '2026-02-04 23:00:39'],
            ['id' => 17, 'user_id' => 1802, 'user_machine_code' => '2531', 'user_department' => 'ادارة الموارد البشرية',             'course_category_id' => 3, 'course_category_name' => 'قسم التسويق',     'course_id' => 6, 'course_name' => 'الكورس الأول',  'course_hours' => 20,  'section_id' => 21, 'attendance_hours' => 10,   'is_manual' => 0, 'created_at' => '2026-02-22 18:39:25', 'updated_at' => '2026-02-05 18:39:25'],
            ['id' => 20, 'user_id' => 1802, 'user_machine_code' => '2531', 'user_department' => 'ادارة الموارد البشرية',             'course_category_id' => 5, 'course_category_name' => 'Academic Courses', 'course_id' => 8, 'course_name' => 'كورس الإدارة',  'course_hours' => 100, 'section_id' => 26, 'attendance_hours' => 50,   'is_manual' => 0, 'created_at' => '2026-02-09 11:41:55', 'updated_at' => '2026-02-09 11:41:55'],
            ['id' => 21, 'user_id' => 1802, 'user_machine_code' => '2531', 'user_department' => 'ادارة الموارد البشرية',             'course_category_id' => 5, 'course_category_name' => 'Academic Courses', 'course_id' => 8, 'course_name' => 'كورس الإدارة',  'course_hours' => 100, 'section_id' => 26, 'attendance_hours' => 50,   'is_manual' => 0, 'created_at' => '2026-02-09 11:42:31', 'updated_at' => '2026-02-09 11:42:31'],
            ['id' => 26, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => 'الحسابات',                       'course_category_id' => 5, 'course_category_name' => 'Academic Courses', 'course_id' => 8, 'course_name' => 'كورس الإدارة',  'course_hours' => 100, 'section_id' => 26, 'attendance_hours' => 50,   'is_manual' => 0, 'created_at' => '2026-02-10 09:10:29', 'updated_at' => '2026-02-10 09:10:29'],
            ['id' => 28, 'user_id' => 1802, 'user_machine_code' => '2531', 'user_department' => 'ادارة الموارد البشرية',             'course_category_id' => 3, 'course_category_name' => 'قسم التسويق',     'course_id' => 6, 'course_name' => 'الكورس الأول',  'course_hours' => 20,  'section_id' => 21, 'attendance_hours' => 10,   'is_manual' => 0, 'created_at' => '2026-02-24 12:15:58', 'updated_at' => '2026-02-19 12:15:58'],
            ['id' => 29, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null,                                'course_category_id' => 5, 'course_category_name' => 'Academic Courses', 'course_id' => 8, 'course_name' => 'كورس الإدارة',  'course_hours' => 100, 'section_id' => 26, 'attendance_hours' => 50,   'is_manual' => 0, 'created_at' => '2026-02-19 12:16:54', 'updated_at' => '2026-02-19 12:16:54'],
            ['id' => 36, 'user_id' => 1761, 'user_machine_code' => '1610', 'user_department' => 'ادارة الموارد البشرية',             'course_category_id' => 5, 'course_category_name' => 'Academic Courses', 'course_id' => 8, 'course_name' => 'كورس الإدارة',  'course_hours' => 100, 'section_id' => 26, 'attendance_hours' => 50,   'is_manual' => 1, 'created_at' => '2026-02-22 14:08:15', 'updated_at' => '2026-02-22 14:08:15'],
            ['id' => 37, 'user_id' => 1761, 'user_machine_code' => '1610', 'user_department' => 'ادارة الموارد البشرية',             'course_category_id' => 5, 'course_category_name' => 'Academic Courses', 'course_id' => 8, 'course_name' => 'كورس الإدارة',  'course_hours' => 100, 'section_id' => 26, 'attendance_hours' => 50,   'is_manual' => 1, 'created_at' => '2026-02-22 14:08:17', 'updated_at' => '2026-02-22 14:08:17'],
        ];

        // `type` column was added by 2026_02_22_155525_add_type_in_attendances_table.
        $payload = array_map(static function (array $row) {
            return $row + (Schema::hasColumn('attendances', 'type') ? ['type' => null] : []);
        }, $rows);

        $this->schemaAwareUpsert('attendances', 
            $payload,
            ['id'],
            ['user_id', 'user_machine_code', 'user_department', 'course_category_id', 'course_category_name', 'course_id', 'course_name', 'course_hours', 'section_id', 'attendance_hours', 'is_manual', 'updated_at'],
        );
    }
}
