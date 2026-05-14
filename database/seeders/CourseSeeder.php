<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 6,
                'course_type' => 'offline',
                'title_ar' => 'الكورس الأول',
                'title_en' => 'First Course',
                'title_cert_ar' => 'الكورس الأول',
                'title_cert_en' => 'First Course',
                'description_ar' => '<p>الكورس الأول الكورس الأول</p>',
                'description_en' => '<p>The first course. The first course.</p>',
                'notification_ar' => null,
                'notification_en' => null,
                'image' => 'Course/HJC3c2hNzmPRXwR9LXZO6b1e3358994bd26f67d8682a8bc4686c.jpg',
                'category_id' => 3,
                'intro_video' => null,
                'price' => null,
                'currency' => null,
                'hours' => 20,
                'language' => 'عربي',
                'level' => 'medium',
                'certificate' => 1,
                'active' => 1,
                'for_public' => 0,
                'allow_attendances' => 0,
                'is_evaluate' => 1,
                'outside_materials' => 0,
                'created_at' => '2026-02-02 19:58:10',
                'updated_at' => '2026-04-01 12:09:26',
            ],
            [
                'id' => 7,
                'course_type' => 'offline',
                'title_ar' => 'الكورس الثاني',
                'title_en' => 'Second Course',
                'title_cert_ar' => 'الكورس الثاني',
                'title_cert_en' => 'Second Course',
                'description_ar' => '<p>الكورس الثاني</p>',
                'description_en' => '<p>The second course.</p>',
                'notification_ar' => null,
                'notification_en' => null,
                'image' => 'Course/8vsq4bin3oYzhTli1cCHf236d07bd8780eb36ad22de451466f17.png',
                'category_id' => 4,
                'intro_video' => null,
                'price' => null,
                'currency' => null,
                'hours' => 20,
                'language' => 'عربي',
                'level' => 'medium',
                'certificate' => 1,
                'active' => 1,
                'for_public' => 0,
                'allow_attendances' => 1,
                'is_evaluate' => 1,
                'outside_materials' => 0,
                'created_at' => '2026-02-03 12:10:58',
                'updated_at' => '2026-02-03 12:10:58',
            ],
            [
                'id' => 8,
                'course_type' => 'offline',
                'title_ar' => 'كورس الإدارة',
                'title_en' => 'Management Course',
                'title_cert_ar' => 'كورس الإدارة',
                'title_cert_en' => 'Management Course',
                'description_ar' => '<p>كورس الإدارة</p>',
                'description_en' => '<p>Management course.</p>',
                'notification_ar' => null,
                'notification_en' => null,
                'image' => 'Course/rFoQaTiiv6uP0JRMHedcaeecd9a4ae32ff5da3fda25e4caf29e8.jpg',
                'category_id' => 5,
                'intro_video' => null,
                'price' => null,
                'currency' => null,
                'hours' => 100,
                'language' => 'عربي',
                'level' => 'medium',
                'certificate' => 1,
                'active' => 1,
                'for_public' => 0,
                'allow_attendances' => 1,
                'is_evaluate' => 1,
                'outside_materials' => 0,
                'created_at' => '2026-02-09 11:09:21',
                'updated_at' => '2026-02-09 11:09:21',
            ],
        ];

        $payload = array_map(function (array $row) {
            return [
                'id' => $row['id'],
                'course_type' => $row['course_type'],
                'title' => $this->json($row['title_ar'], $row['title_en']),
                'description' => $this->json($row['description_ar'], $row['description_en']),
                'title_for_certificate' => $this->jsonNullable($row['title_cert_ar'], $row['title_cert_en']),
                'notification_text' => $this->jsonNullable($row['notification_ar'], $row['notification_en']),
                'title_backup' => $row['title_ar'],
                'description_backup' => $row['description_ar'],
                'title_for_certificate_backup' => $row['title_cert_ar'],
                'notification_text_backup' => $row['notification_ar'],
                'image' => $row['image'],
                'category_id' => $row['category_id'],
                'intro_video' => $row['intro_video'],
                'price' => $row['price'],
                'currency' => $row['currency'],
                'hours' => $row['hours'],
                'language' => $row['language'],
                'level' => $row['level'],
                'certificate' => $row['certificate'],
                'active' => $row['active'],
                'for_public' => $row['for_public'],
                'allow_attendances' => $row['allow_attendances'],
                'is_evaluate' => $row['is_evaluate'],
                'outside_materials' => $row['outside_materials'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];
        }, $rows);

        $this->schemaAwareUpsert('courses', 
            $payload,
            ['id'],
            [
                'course_type', 'title', 'description', 'title_for_certificate', 'notification_text',
                'title_backup', 'description_backup', 'title_for_certificate_backup', 'notification_text_backup',
                'image', 'category_id', 'intro_video', 'price', 'currency', 'hours', 'language', 'level',
                'certificate', 'active', 'for_public', 'allow_attendances', 'is_evaluate', 'outside_materials',
                'updated_at',
            ]
        );
    }

    private function json(string $ar, string $en): string
    {
        return json_encode(['ar' => $ar, 'en' => $en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function jsonNullable(?string $ar, ?string $en): ?string
    {
        if ($ar === null && $en === null) {
            return null;
        }

        return json_encode(['ar' => $ar ?? '', 'en' => $en ?? ''], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
