<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Most legacy form questions were placeholder text dumped during demo
 * sessions (e.g. "يسب", "dsfsd", lorem-ipsum strings). We keep the
 * raw source in the Arabic slot and provide a reasonable English mirror
 * so the JSON columns introduced by the localization migrations have
 * both keys populated.
 */
class FormQuestionSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            ['id' => 10, 'form_id' => 7, 'type' => 'radio',  'ar' => 'يسب',                  'en' => 'Sample radio question 1',     'created_at' => '2026-02-10 19:46:54'],
            ['id' => 11, 'form_id' => 7, 'type' => 'yes_no', 'ar' => 'dsfsd',                'en' => 'Sample yes/no question 1',    'created_at' => '2026-02-10 19:47:17'],
            ['id' => 13, 'form_id' => 7, 'type' => 'text',   'ar' => 'تيكست',                'en' => 'Free-text answer',            'created_at' => '2026-02-10 21:10:55'],
            ['id' => 14, 'form_id' => 7, 'type' => 'radio',  'ar' => 'aa',                   'en' => 'Sample radio question 2',     'created_at' => '2026-04-01 11:48:04'],
            ['id' => 15, 'form_id' => 7, 'type' => 'radio',  'ar' => 'يسب',                  'en' => 'Sample radio question 3',     'created_at' => '2026-04-27 12:56:12'],
            ['id' => 16, 'form_id' => 7, 'type' => 'radio',  'ar' => 'Labore excepteur nem', 'en' => 'Labore excepteur nem',        'created_at' => '2026-04-27 13:02:24'],
            ['id' => 17, 'form_id' => 7, 'type' => 'yes_no', 'ar' => 'Possimus architecto',  'en' => 'Possimus architecto',         'created_at' => '2026-04-27 13:02:40'],
            ['id' => 18, 'form_id' => 7, 'type' => 'radio',  'ar' => 'Explicabo Vitae cil',  'en' => 'Explicabo Vitae cil',         'created_at' => '2026-04-27 13:03:46'],
            ['id' => 19, 'form_id' => 7, 'type' => 'yes_no', 'ar' => 'Quo atque ut neque s', 'en' => 'Quo atque ut neque s',        'created_at' => '2026-04-27 13:04:17'],
            ['id' => 20, 'form_id' => 8, 'type' => 'yes_no', 'ar' => 'نعم',                  'en' => 'Yes / No question',           'created_at' => '2026-04-27 14:39:00'],
            ['id' => 21, 'form_id' => 8, 'type' => 'radio',  'ar' => '3',                    'en' => 'Choose option',               'created_at' => '2026-04-27 14:39:20'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'form_id' => $row['form_id'],
            'type' => $row['type'],
            'question' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'question_backup' => $row['ar'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['created_at'],
        ], $rows);

        $this->schemaAwareUpsert('form_questions', $payload, ['id'], ['form_id', 'type', 'question', 'question_backup', 'updated_at']);
    }
}
