<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $nameAr = 'محمد سعيد';
        $nameEn = 'Mohamed Said';
        $descriptionAr = '<p>"لقد التحقتُ بالعديد من الدورات، وقد فاقت كلٌّ منها توقعاتي. اكتسبتُ مهاراتٍ قيّمة ساعدتني على التقدم في مسيرتي المهنية. أنصح بها بشدة.!"</p>';
        $descriptionEn = '<p>"I have enrolled in many courses, and each of them exceeded my expectations. I picked up valuable skills that helped me advance in my career. Highly recommended!"</p>';

        $this->schemaAwareUpsert('testimonials', [
            [
                'id' => 1,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
                'description_ar' => $descriptionAr,
                'description_en' => $descriptionEn,
                'name' => $this->json($nameAr, $nameEn),
                'description' => $this->json($descriptionAr, $descriptionEn),
                'image' => 'Testimonial/fQPxwESGYtrnYl0BAMmJa38cc020bb9d6059f76d2a175c2453c3.png',
                'active' => 1,
                'created_at' => '2025-08-07 13:48:08',
                'updated_at' => '2025-08-07 13:48:47',
            ],
        ], ['id'], ['name_ar', 'name_en', 'description_ar', 'description_en', 'name', 'description', 'image', 'active', 'updated_at']);
    }

    private function json(string $ar, string $en): string
    {
        return json_encode(
            ['ar' => $ar, 'en' => $en],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
