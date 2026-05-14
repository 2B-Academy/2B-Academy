<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationCategorySeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            ['id' => 2, 'ar' => 'تقييم  المحاضر', 'en' => 'Instructor Evaluation', 'created_at' => '2026-01-31 12:19:53'],
            ['id' => 3, 'ar' => 'تقييم الكورس',  'en' => 'Course Evaluation',     'created_at' => '2026-02-03 08:54:03'],
            ['id' => 4, 'ar' => 'NPS',              'en' => 'NPS',                    'created_at' => '2026-02-09 11:27:00'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'name' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'name_backup' => $row['ar'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['created_at'],
        ], $rows);

        $this->schemaAwareUpsert('evaluation_categories', $payload, ['id'], ['name', 'name_backup', 'updated_at']);
    }
}
