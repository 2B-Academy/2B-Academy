<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSectionSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            ['id' => 20, 'course_id' => 6, 'ar' => 'المجموعة الأولي', 'en' => 'First Group', 'created_at' => '2026-02-02 19:58:41', 'updated_at' => '2026-02-02 19:58:41'],
            ['id' => 21, 'course_id' => 6, 'ar' => 'المجموعة الثانية', 'en' => 'Second Group', 'created_at' => '2026-02-02 19:58:41', 'updated_at' => '2026-02-02 19:58:41'],
            ['id' => 22, 'course_id' => 6, 'ar' => 'المجموعة الثالثة', 'en' => 'Third Group', 'created_at' => '2026-02-02 19:58:41', 'updated_at' => '2026-02-02 19:58:41'],
            ['id' => 23, 'course_id' => 7, 'ar' => 'المجموعة الأولي', 'en' => 'First Group', 'created_at' => '2026-02-03 12:11:21', 'updated_at' => '2026-02-03 12:11:21'],
            ['id' => 24, 'course_id' => 7, 'ar' => 'المجموعة الثانية', 'en' => 'Second Group', 'created_at' => '2026-02-03 12:11:21', 'updated_at' => '2026-02-03 12:11:21'],
            ['id' => 25, 'course_id' => 7, 'ar' => 'اختبار نهائي', 'en' => 'Final Exam', 'created_at' => '2026-02-03 12:13:31', 'updated_at' => '2026-02-03 12:13:31'],
            ['id' => 26, 'course_id' => 8, 'ar' => 'المجموعة الأولي', 'en' => 'First Group', 'created_at' => '2026-02-09 11:10:24', 'updated_at' => '2026-02-09 11:10:24'],
            ['id' => 27, 'course_id' => 8, 'ar' => 'المجموعة الثانية', 'en' => 'Second Group', 'created_at' => '2026-02-09 11:10:24', 'updated_at' => '2026-02-09 11:10:24'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'course_id' => $row['course_id'],
            'name' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'name_backup' => $row['ar'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);

        $this->schemaAwareUpsert('course_sections', $payload, ['id'], ['course_id', 'name', 'name_backup', 'updated_at']);
    }
}
