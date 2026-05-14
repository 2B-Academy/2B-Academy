<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;

class CourseExamSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 10,
                'course_id' => 7,
                'section_id' => 25,
                'ar' => 'اختبار نهائي علي الكورس الثاني',
                'en' => 'Final exam for the Second Course',
                'degree' => 100,
                'duration' => 60,
                'is_final' => 1,
                'created_at' => '2026-02-03 12:14:15',
                'updated_at' => '2026-02-03 12:14:15',
            ],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'course_id' => $row['course_id'],
            'section_id' => $row['section_id'],
            'title' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'title_backup' => $row['ar'],
            'degree' => $row['degree'],
            'duration' => $row['duration'],
            'is_final' => $row['is_final'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);

        $this->schemaAwareUpsert('course_exams', $payload, ['id'], [
            'course_id', 'section_id', 'title', 'title_backup', 'degree', 'duration', 'is_final', 'updated_at',
        ]);
    }
}
