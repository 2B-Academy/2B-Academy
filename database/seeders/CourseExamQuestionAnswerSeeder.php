<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseExamQuestionAnswerSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            ['id' => 140, 'question_id' => 42, 'ar' => 'المشروعات', 'en' => 'Projects', 'is_correct' => 1, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 141, 'question_id' => 42, 'ar' => 'البحوث', 'en' => 'Research', 'is_correct' => 0, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 142, 'question_id' => 42, 'ar' => 'التسويق', 'en' => 'Marketing', 'is_correct' => 0, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 143, 'question_id' => 42, 'ar' => 'PMP', 'en' => 'PMP', 'is_correct' => 0, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 144, 'question_id' => 43, 'ar' => 'المبيعات', 'en' => 'Sales', 'is_correct' => 1, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 145, 'question_id' => 43, 'ar' => 'الإعلانات', 'en' => 'Advertising', 'is_correct' => 0, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 146, 'question_id' => 43, 'ar' => 'الإدارة', 'en' => 'Management', 'is_correct' => 0, 'created_at' => '2026-02-03 12:14:15'],
            ['id' => 147, 'question_id' => 43, 'ar' => 'البرمجة', 'en' => 'Programming', 'is_correct' => 0, 'created_at' => '2026-02-03 12:14:15'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'question_id' => $row['question_id'],
            'answer' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'answer_backup' => $row['ar'],
            'is_correct' => $row['is_correct'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['created_at'],
        ], $rows);

        $this->schemaAwareUpsert('course_exam_question_answers', $payload, ['id'], ['question_id', 'answer', 'answer_backup', 'is_correct', 'updated_at']);
    }
}
