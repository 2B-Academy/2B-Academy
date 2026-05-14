<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstructorSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 1,
                'name_ar' => 'محمد سعيد',
                'name_en' => 'Mohamed Said',
                'email' => 'mohamedsaid11129@gmail.com',
                'image' => 'Instructor/Ayf5J8zawG5ZlRTYNOH9f4671ef1bea584118ee4623d6c577a98.png',
                'bio_ar' => '<p>محمد سعيد محمد سعيد محمد سعيد</p>',
                'bio_en' => '<p>Mohamed Said. Mohamed Said. Mohamed Said.</p>',
                'job_title_ar' => 'مهندس برمجة',
                'job_title_en' => 'Software Engineer',
                'created_at' => '2025-08-06 12:08:29',
                'updated_at' => '2025-08-06 12:08:50',
            ],
            [
                'id' => 2,
                'name_ar' => 'كريم حسن',
                'name_en' => 'Karim Hassan',
                'email' => 'k@hassan.com',
                'image' => 'Instructor/J1MivWmo89BmmCaODRJR630a1e36f1a380d43a985996c79c8044.png',
                'bio_ar' => '<p>كريم حسن كريم حسن كريم حسن</p>',
                'bio_en' => '<p>Karim Hassan. Karim Hassan. Karim Hassan.</p>',
                'job_title_ar' => 'مدير مشروع',
                'job_title_en' => 'Project Manager',
                'created_at' => '2025-08-06 12:09:31',
                'updated_at' => '2025-08-06 12:09:31',
            ],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'name' => self::json($row['name_ar'], $row['name_en']),
            'bio' => self::json($row['bio_ar'], $row['bio_en']),
            'job_title' => self::json($row['job_title_ar'], $row['job_title_en']),
            'name_backup' => $row['name_ar'],
            'bio_backup' => $row['bio_ar'],
            'job_title_backup' => $row['job_title_ar'],
            'email' => $row['email'],
            'image' => $row['image'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);

        $this->schemaAwareUpsert('instructors', 
            $payload,
            ['id'],
            ['name', 'bio', 'job_title', 'name_backup', 'bio_backup', 'job_title_backup', 'email', 'image', 'updated_at'],
        );
    }

    private static function json(string $ar, string $en): string
    {
        return json_encode(['ar' => $ar, 'en' => $en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
