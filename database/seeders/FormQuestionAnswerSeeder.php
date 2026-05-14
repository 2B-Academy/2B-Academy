<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormQuestionAnswerSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            ['id' => 33, 'form_question_id' => 10, 'ar' => 'يسب', 'en' => 'Option A', 'is_true' => 1, 'created_at' => '2026-02-10 19:46:54'],
            ['id' => 34, 'form_question_id' => 10, 'ar' => 'يب',  'en' => 'Option B', 'is_true' => 0, 'created_at' => '2026-02-10 19:46:54'],
            ['id' => 35, 'form_question_id' => 10, 'ar' => 'يسب', 'en' => 'Option C', 'is_true' => 0, 'created_at' => '2026-02-10 19:46:54'],
            ['id' => 36, 'form_question_id' => 10, 'ar' => 'يب',  'en' => 'Option D', 'is_true' => 0, 'created_at' => '2026-02-10 19:46:54'],
            ['id' => 37, 'form_question_id' => 11, 'ar' => 'نعم', 'en' => 'Yes',      'is_true' => 1, 'created_at' => '2026-02-10 19:47:17'],
            ['id' => 38, 'form_question_id' => 11, 'ar' => 'لا',  'en' => 'No',       'is_true' => 0, 'created_at' => '2026-02-10 19:47:17'],
            ['id' => 39, 'form_question_id' => 14, 'ar' => 'a',   'en' => 'a',        'is_true' => 0, 'created_at' => '2026-04-01 11:48:04'],
            ['id' => 40, 'form_question_id' => 14, 'ar' => 'b',   'en' => 'b',        'is_true' => 1, 'created_at' => '2026-04-01 11:48:04'],
            ['id' => 41, 'form_question_id' => 14, 'ar' => 'c',   'en' => 'c',        'is_true' => 0, 'created_at' => '2026-04-01 11:48:04'],
            ['id' => 42, 'form_question_id' => 14, 'ar' => 'd',   'en' => 'd',        'is_true' => 0, 'created_at' => '2026-04-01 11:48:04'],
            ['id' => 43, 'form_question_id' => 15, 'ar' => 'aa',  'en' => 'aa',       'is_true' => 0, 'created_at' => '2026-04-27 12:56:12'],
            ['id' => 44, 'form_question_id' => 15, 'ar' => 'vb',  'en' => 'vb',       'is_true' => 1, 'created_at' => '2026-04-27 12:56:12'],
            ['id' => 45, 'form_question_id' => 15, 'ar' => 'as',  'en' => 'as',       'is_true' => 0, 'created_at' => '2026-04-27 12:56:12'],
            ['id' => 46, 'form_question_id' => 15, 'ar' => '-',   'en' => '-',        'is_true' => 0, 'created_at' => '2026-04-27 12:56:12'],
            ['id' => 47, 'form_question_id' => 16, 'ar' => 'Consequatur eius qua', 'en' => 'Consequatur eius qua', 'is_true' => 1, 'created_at' => '2026-04-27 13:02:24'],
            ['id' => 48, 'form_question_id' => 16, 'ar' => 'Sunt deserunt quaer',  'en' => 'Sunt deserunt quaer',  'is_true' => 0, 'created_at' => '2026-04-27 13:02:24'],
            ['id' => 49, 'form_question_id' => 16, 'ar' => 'Proident fugiat rep', 'en' => 'Proident fugiat rep',  'is_true' => 0, 'created_at' => '2026-04-27 13:02:24'],
            ['id' => 50, 'form_question_id' => 16, 'ar' => '-',                    'en' => '-',                    'is_true' => 0, 'created_at' => '2026-04-27 13:02:24'],
            ['id' => 51, 'form_question_id' => 17, 'ar' => 'نعم', 'en' => 'Yes', 'is_true' => 0, 'created_at' => '2026-04-27 13:02:40'],
            ['id' => 52, 'form_question_id' => 17, 'ar' => 'لا',  'en' => 'No',  'is_true' => 1, 'created_at' => '2026-04-27 13:02:40'],
            ['id' => 53, 'form_question_id' => 18, 'ar' => 'Proident numquam in', 'en' => 'Proident numquam in', 'is_true' => 0, 'created_at' => '2026-04-27 13:03:46'],
            ['id' => 54, 'form_question_id' => 18, 'ar' => 'Magni enim deserunt', 'en' => 'Magni enim deserunt', 'is_true' => 0, 'created_at' => '2026-04-27 13:03:46'],
            ['id' => 55, 'form_question_id' => 18, 'ar' => 'Enim ut fugit dolor', 'en' => 'Enim ut fugit dolor', 'is_true' => 1, 'created_at' => '2026-04-27 13:03:46'],
            ['id' => 56, 'form_question_id' => 18, 'ar' => 'Debitis non minus bl', 'en' => 'Debitis non minus bl', 'is_true' => 0, 'created_at' => '2026-04-27 13:03:46'],
            ['id' => 57, 'form_question_id' => 19, 'ar' => 'نعم', 'en' => 'Yes', 'is_true' => 1, 'created_at' => '2026-04-27 13:04:17'],
            ['id' => 58, 'form_question_id' => 19, 'ar' => 'لا',  'en' => 'No',  'is_true' => 0, 'created_at' => '2026-04-27 13:04:17'],
            ['id' => 59, 'form_question_id' => 20, 'ar' => 'نعم', 'en' => 'Yes', 'is_true' => 1, 'created_at' => '2026-04-27 14:39:00'],
            ['id' => 60, 'form_question_id' => 20, 'ar' => 'لا',  'en' => 'No',  'is_true' => 0, 'created_at' => '2026-04-27 14:39:00'],
            ['id' => 61, 'form_question_id' => 21, 'ar' => '1',   'en' => '1',   'is_true' => 0, 'created_at' => '2026-04-27 14:39:20'],
            ['id' => 62, 'form_question_id' => 21, 'ar' => '2',   'en' => '2',   'is_true' => 0, 'created_at' => '2026-04-27 14:39:20'],
            ['id' => 63, 'form_question_id' => 21, 'ar' => '3',   'en' => '3',   'is_true' => 1, 'created_at' => '2026-04-27 14:39:20'],
            ['id' => 64, 'form_question_id' => 21, 'ar' => '4',   'en' => '4',   'is_true' => 0, 'created_at' => '2026-04-27 14:39:20'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'form_question_id' => $row['form_question_id'],
            'answer' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'answer_backup' => $row['ar'],
            'is_true' => $row['is_true'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['created_at'],
        ], $rows);

        $this->schemaAwareUpsert('form_question_answers', $payload, ['id'], ['form_question_id', 'answer', 'answer_backup', 'is_true', 'updated_at']);
    }
}
