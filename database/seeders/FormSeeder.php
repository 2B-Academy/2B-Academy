<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 7,
                'uuid' => 'c23239c5-bf7e-4c41-8878-086fe4907241',
                'ar' => 'الأختبار الأول',
                'en' => 'First Exam',
                'duration' => 60,
                'full_mark' => 100,
                'active' => 1,
                'created_at' => '2026-02-10 19:44:45',
                'updated_at' => '2026-02-10 19:44:45',
            ],
            [
                'id' => 8,
                'uuid' => '9214cafd-5fb8-4eab-9347-f23ceb21101c',
                'ar' => 'اختبار تجريبي',
                'en' => 'Test Exam',
                'duration' => 100,
                'full_mark' => 100,
                'active' => 1,
                'created_at' => '2026-04-27 14:38:40',
                'updated_at' => '2026-04-27 14:38:40',
            ],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'uuid' => $row['uuid'],
            'title' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'title_backup' => $row['ar'],
            'duration' => $row['duration'],
            'full_mark' => $row['full_mark'],
            'active' => $row['active'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);

        $this->schemaAwareUpsert('forms', $payload, ['id'], ['uuid', 'title', 'title_backup', 'duration', 'full_mark', 'active', 'updated_at']);
    }
}
