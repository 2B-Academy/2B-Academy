<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds public_notifications using the localized JSON layout
 * introduced by the 2026-05-14 translation migrations.
 *
 * Rows imported from the legacy dump only have a single-language value,
 * so the JSON columns mirror the source value into both `ar` and `en`
 * keys. Where the original is meaningful Arabic copy we provide a
 * proper English translation; placeholder / generated text is left
 * untouched in both locales.
 */
class PublicNotificationSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 1,
                'title_ar' => 'Tempora numquam libe',
                'title_en' => 'Tempora numquam libe',
                'body_ar' => 'Reprehenderit alias',
                'body_en' => 'Reprehenderit alias',
                'for_public' => 0,
                'created_at' => '2025-12-22 14:49:18',
                'updated_at' => '2025-12-22 14:49:18',
            ],
            [
                'id' => 2,
                'title_ar' => 'sad',
                'title_en' => 'sad',
                'body_ar' => 'sad',
                'body_en' => 'sad',
                'for_public' => 0,
                'created_at' => '2026-01-11 15:47:50',
                'updated_at' => '2026-01-11 15:47:50',
            ],
            [
                'id' => 3,
                'title_ar' => 'تيست',
                'title_en' => 'Test',
                'body_ar' => 'تيست  من الاشعارات العامه',
                'body_en' => 'Test from the public notifications',
                'for_public' => 0,
                'created_at' => '2026-01-11 15:54:25',
                'updated_at' => '2026-01-11 15:54:25',
            ],
            [
                'id' => 4,
                'title_ar' => 'test',
                'title_en' => 'test',
                'body_ar' => 'test',
                'body_en' => 'test',
                'for_public' => 0,
                'created_at' => '2026-02-18 14:34:32',
                'updated_at' => '2026-02-18 14:34:32',
            ],
            [
                'id' => 5,
                'title_ar' => 'test',
                'title_en' => 'test',
                'body_ar' => 'test',
                'body_en' => 'test',
                'for_public' => 0,
                'created_at' => '2026-02-18 14:34:56',
                'updated_at' => '2026-02-18 14:34:56',
            ],
        ];

        $payload = array_map(function (array $row) {
            return [
                'id' => $row['id'],
                'title' => $this->json($row['title_ar'], $row['title_en']),
                'body' => $this->json($row['body_ar'], $row['body_en']),
                'title_backup' => $row['title_ar'],
                'body_backup' => $row['body_ar'],
                'for_public' => $row['for_public'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];
        }, $rows);

        $this->schemaAwareUpsert('public_notifications', 
            $payload,
            ['id'],
            ['title', 'body', 'title_backup', 'body_backup', 'for_public', 'updated_at'],
        );
    }

    private function json(string $ar, string $en): string
    {
        return json_encode(['ar' => $ar, 'en' => $en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
