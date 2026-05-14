<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFormAnswerSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('user_form_answers', [
            ['id' => 18, 'user_form_id' => 6, 'question_id' => 10, 'question' => 'يسب',                  'answer_id' => 34,   'answer' => 'يسب',                  'is_true' => 0, 'created_at' => '2026-02-10 21:46:33', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 19, 'user_form_id' => 6, 'question_id' => 11, 'question' => 'dsfsd',                'answer_id' => 37,   'answer' => 'نعم',                  'is_true' => 1, 'created_at' => '2026-02-10 21:46:33', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 20, 'user_form_id' => 6, 'question_id' => 13, 'question' => 'تيكست',                'answer_id' => null, 'answer' => 'فثسف',                'is_true' => 1, 'created_at' => '2026-02-10 21:46:33', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 21, 'user_form_id' => 7, 'question_id' => 10, 'question' => 'يسب',                  'answer_id' => 34,   'answer' => 'يسب',                  'is_true' => 0, 'created_at' => '2026-02-10 21:46:33', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 22, 'user_form_id' => 7, 'question_id' => 11, 'question' => 'dsfsd',                'answer_id' => 37,   'answer' => 'نعم',                  'is_true' => 1, 'created_at' => '2026-02-10 21:46:33', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 23, 'user_form_id' => 7, 'question_id' => 13, 'question' => 'تيكست',                'answer_id' => null, 'answer' => 'فثسف',                'is_true' => 1, 'created_at' => '2026-02-10 21:46:33', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 24, 'user_form_id' => 8, 'question_id' => 10, 'question' => 'يسب',                  'answer_id' => 34,   'answer' => 'يب',                   'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 25, 'user_form_id' => 8, 'question_id' => 11, 'question' => 'dsfsd',                'answer_id' => 38,   'answer' => 'لا',                   'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 26, 'user_form_id' => 8, 'question_id' => 13, 'question' => 'تيكست',                'answer_id' => null, 'answer' => 'test',                 'is_true' => 1, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 27, 'user_form_id' => 8, 'question_id' => 14, 'question' => 'aa',                   'answer_id' => 42,   'answer' => 'd',                    'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 28, 'user_form_id' => 8, 'question_id' => 15, 'question' => 'يسب',                  'answer_id' => 46,   'answer' => '-',                    'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 29, 'user_form_id' => 8, 'question_id' => 16, 'question' => 'Labore excepteur nem', 'answer_id' => 49,   'answer' => 'Proident fugiat rep', 'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 30, 'user_form_id' => 8, 'question_id' => 17, 'question' => 'Possimus architecto',  'answer_id' => 51,   'answer' => 'نعم',                  'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 31, 'user_form_id' => 8, 'question_id' => 18, 'question' => 'Explicabo Vitae cil',  'answer_id' => 55,   'answer' => 'Enim ut fugit dolor',  'is_true' => 1, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 32, 'user_form_id' => 8, 'question_id' => 19, 'question' => 'Quo atque ut neque s', 'answer_id' => 58,   'answer' => 'لا',                   'is_true' => 0, 'created_at' => '2026-04-27 14:15:28', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 33, 'user_form_id' => 9, 'question_id' => 20, 'question' => 'yes',                  'answer_id' => 60,   'answer' => 'لا',                   'is_true' => 0, 'created_at' => '2026-04-27 14:39:43', 'updated_at' => '2026-04-27 14:39:43'],
            ['id' => 34, 'user_form_id' => 9, 'question_id' => 21, 'question' => '3',                    'answer_id' => 62,   'answer' => '2',                    'is_true' => 0, 'created_at' => '2026-04-27 14:39:43', 'updated_at' => '2026-04-27 14:39:43'],
        ], ['id'], ['user_form_id', 'question_id', 'question', 'answer_id', 'answer', 'is_true', 'updated_at']);
    }
}
