<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseExamQuestionSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 42,
                'course_exam_id' => 10,
                'ar' => 'السؤال الأول : ما معنى الإدارة ؟',
                'en' => 'Question 1: What does Management mean?',
                'created_at' => '2026-02-03 12:14:15',
                'updated_at' => '2026-02-03 12:14:15',
            ],
            [
                'id' => 43,
                'course_exam_id' => 10,
                'ar' => 'السؤال الأول : ما معنى التسويق؟',
                'en' => 'Question 1: What does Marketing mean?',
                'created_at' => '2026-02-03 12:14:15',
                'updated_at' => '2026-02-03 12:14:15',
            ],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'course_exam_id' => $row['course_exam_id'],
            'question' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'question_backup' => $row['ar'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);

        $this->schemaAwareUpsert('course_exam_questions', $payload, ['id'], ['course_exam_id', 'question', 'question_backup', 'updated_at']);
    }
}
