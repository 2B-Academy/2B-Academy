<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserExamAnswerSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('user_exam_answers', [
            [
                'id' => 31,
                'user_exam_id' => 8,
                'question_id' => 42,
                'question' => 'السؤال الأول : ما معنى الإدارة ؟',
                'answer_id' => 140,
                'answer' => 'المشروعات',
                'is_correct' => 1,
                'created_at' => '2026-02-03 12:19:31',
                'updated_at' => '2026-02-03 12:19:31',
            ],
            [
                'id' => 32,
                'user_exam_id' => 8,
                'question_id' => 43,
                'question' => 'السؤال الأول : ما معنى التسويق؟',
                'answer_id' => 144,
                'answer' => 'المبيعات',
                'is_correct' => 1,
                'created_at' => '2026-02-03 12:19:31',
                'updated_at' => '2026-02-03 12:19:31',
            ],
        ], ['id'], ['user_exam_id', 'question_id', 'question', 'answer_id', 'answer', 'is_correct', 'updated_at']);
    }
}
