<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 3,
                'ar' => 'قسم التسويق',
                'en' => 'Marketing Department',
                'logo' => 'Category/uGWbdRAVXoaYxLuE28MHfc400d6e78ac1cbbe0d7d69e680e758e.png',
                'active' => 1,
                'created_at' => '2025-09-10 11:07:09',
                'updated_at' => '2025-09-10 11:07:09',
            ],
            [
                'id' => 4,
                'ar' => 'قسم البرمجة',
                'en' => 'Programming Department',
                'logo' => 'Category/uGWbdRAVXoaYxLuE28MHfc400d6e78ac1cbbe0d7d69e680e758e.png',
                'active' => 1,
                'created_at' => '2025-09-10 11:07:09',
                'updated_at' => '2025-09-10 11:07:09',
            ],
            [
                'id' => 5,
                'ar' => 'الدورات الأكاديمية',
                'en' => 'Academic Courses',
                'logo' => 'Category/QFlcSLKJW2zJFujgEZWv0ece728009845ea10beba3c282c229e1.png',
                'active' => 1,
                'created_at' => '2026-02-09 11:05:05',
                'updated_at' => '2026-02-09 11:05:05',
            ],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'name' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'name_backup' => $row['ar'],
            'logo' => $row['logo'],
            'active' => $row['active'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);

        $this->schemaAwareUpsert('categories', $payload, ['id'], ['name', 'name_backup', 'logo', 'active', 'updated_at']);
    }
}
