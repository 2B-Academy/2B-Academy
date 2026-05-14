<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $articles = [
            [
                'id' => 1,
                'type' => 'blogs',
                'title_ar' => 'المقالة الأولي',
                'title_en' => 'First Article',
                'description_ar' => '<p>المقالة الأولي المقالة الأولي المقالة الأولي</p>',
                'description_en' => '<p>The first article. The first article. The first article.</p>',
                'slug' => 'المقالة-الأولي',
                'date_publish' => '2025-08-01',
                'image' => 'Article/GwLyLODpP5G7yC3G0yCX6a7f2cb00a634a200b8ed48827c822e6.png',
                'is_home' => 1,
                'active' => 1,
                'created_at' => '2025-08-07 13:40:20',
                'updated_at' => '2025-08-07 13:41:44',
            ],
            [
                'id' => 2,
                'type' => 'blogs',
                'title_ar' => 'المقالة الثانية',
                'title_en' => 'Second Article',
                'description_ar' => '<p>المقالة الأولي المقالة الأولي المقالة الأولي</p>',
                'description_en' => '<p>The second article. The second article. The second article.</p>',
                'slug' => 'المقالة-الثانية',
                'date_publish' => '2025-08-01',
                'image' => 'Article/GwLyLODpP5G7yC3G0yCX6a7f2cb00a634a200b8ed48827c822e6.png',
                'is_home' => 1,
                'active' => 1,
                'created_at' => '2025-08-07 13:40:20',
                'updated_at' => '2025-08-07 13:41:44',
            ],
        ];

        $payload = array_map(function (array $row) {
            $row['title'] = $this->json($row['title_ar'], $row['title_en']);
            $row['description'] = $this->json($row['description_ar'], $row['description_en']);

            return $row;
        }, $articles);

        $this->schemaAwareUpsert('articles', $payload, ['id'], [
            'type', 'title_ar', 'title_en', 'description_ar', 'description_en',
            'title', 'description', 'slug', 'date_publish', 'image', 'is_home', 'active', 'updated_at',
        ]);
    }

    private function json(string $ar, string $en): string
    {
        return json_encode(
            ['ar' => $ar, 'en' => $en],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
