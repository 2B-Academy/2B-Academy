<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key'], 'module' => $row['module']],
                $row
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            [
                'id' => 23,
                'type' => 'file',
                'label' => 'لوجو الهيدر',
                'key' => 'header_logo',
                'value' => 'Setting/QruQxuS5SfEsRsMkHW0Q97a620276acda9aaef889ccdeb423e4d.png',
                'module' => 'home',
                'created_at' => '2025-08-11 21:34:53',
                'updated_at' => '2025-08-11 21:41:38',
            ],
            [
                'id' => 24,
                'type' => 'file',
                'label' => 'صورة البانر',
                'key' => 'banner_background',
                'value' => 'Setting/oERVJ0gZg283yMxtDNzUa2d6f727c5eaa7c64c63386a44a8f00b.jpg',
                'module' => 'home',
                'created_at' => '2025-09-07 17:55:02',
                'updated_at' => '2025-09-07 17:59:12',
            ],
            [
                'id' => 25,
                'type' => 'textarea',
                'label' => 'وصف البانر',
                'key' => 'banner_description',
                'value' => "<h5 class=\"text-main-600 mb-0\">ارتقِ بمستوى تعلمك</h5>\r\n<h1 class=\"display2 mb-24 wow bounceInLeft\">تعلم,&nbsp;<span class=\"text-main-two-600 wow bounceInRight\" data-wow-duration=\"2s\" data-wow-delay=\".5s\">تنمو,</span>&nbsp;<span class=\"text-main-three-600 wow bounceInLeft\" data-wow-duration=\"1s\" data-wow-delay=\".5s\">حقق</span>&nbsp;وانجح</h1>\r\n<p>أهلاً بكم في توبي حيث لا حدود للتعلم. سواءً كنت طالبًا، أو محترفًا، أو متعلمًا مدى الحياة..</p>",
                'module' => 'home',
                'created_at' => '2025-08-11 21:34:53',
                'updated_at' => '2026-02-19 12:29:16',
            ],
            [
                'id' => 26,
                'type' => 'file',
                'label' => 'الشهادة',
                'key' => 'certificate',
                'value' => 'Setting/a1JKpvYV2OmBZr1xe4hjcadf2ebfbe3bb67a4089e222013566fd.jpg',
                'module' => 'home',
                'created_at' => '2025-08-11 21:34:53',
                'updated_at' => '2025-09-20 09:57:36',
            ],
            [
                'id' => 27,
                'type' => 'textarea',
                'label' => 'محتوي الشهادة',
                'key' => 'certificate_content',
                'value' => "<div class=\"certificate-two-item animation-item border-bottom border-neutral-50 border-dashed border-0 mb-28 pb-28 aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"200\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">تعلم من خبراء الصناعة</h5>\r\n</div>\r\n<p class=\"text-neutral-700 text-line-2\">&nbsp;</p>\r\n</div>\r\n<div class=\"certificate-two-item animation-item border-bottom border-neutral-50 border-dashed border-0 mb-28 pb-28 aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"400\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">تعلم في أي وقت وفي أي مكان</h5>\r\n</div>\r\n<p class=\"text-neutral-700 text-line-2\">&nbsp;</p>\r\n</div>\r\n<div class=\"certificate-two-item animation-item border-bottom border-neutral-50 border-dashed border-0 mb-28 pb-28 aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"600\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">مصادر مجانية</h5>\r\n</div>\r\n<p class=\"text-neutral-700 text-line-2\">&nbsp;</p>\r\n</div>\r\n<div class=\"certificate-two-item animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"800\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">التعلم القائم على المهارات</h5>\r\n</div>\r\n</div>",
                'module' => 'home',
                'created_at' => '2025-08-11 21:34:53',
                'updated_at' => '2026-02-19 12:29:16',
            ],
            [
                'id' => 28,
                'type' => 'file',
                'label' => 'لوجو الفوتر',
                'key' => 'footer_logo',
                'value' => 'Setting/Jt7f3bXyBe8T2ZZZwF1Me04620b79ad80f7e798498de925e54ab.png',
                'module' => 'home',
                'created_at' => '2025-08-11 21:34:53',
                'updated_at' => '2025-08-12 10:33:23',
            ],
            [
                'id' => 29,
                'type' => 'textarea',
                'label' => 'لماذا نحن',
                'key' => 'why_us',
                'value' => "<div class=\"mb-40\">\r\n<div class=\"flex-align d-inline-flex gap-8 mb-16 wow bounceInDown\">\r\n<h5 class=\"text-main-600 mb-0\">لماذا نحن</h5>\r\n</div>\r\n<h2 class=\"mb-24 wow bounceIn\">أكثر من 16 عامًا في التعلم عن بعد لتنمية المهارات</h2>\r\n<p class=\"text-neutral-500 text-line-2 wow bounceInUp\">نحن شغوفون بتغيير حياة الناس من خلال التعليم. تأسست مؤسستنا برؤية تهدف إلى جعل التعلم في متناول الجميع، ونؤمن بقدرة المعرفة على فتح الفرص ورسم ملامح المستقبل..</p>\r\n</div>\r\n<div class=\"grid-cols-2\">\r\n<div class=\"flex-align align-items-start gap-20 animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"600\"><span class=\"flex-shrink-0 w-60 h-60 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md\"><img class=\"animate__swing\" src=\"assets/images/icons/choose-us-icon1.png\" alt=\"\" /></span>\r\n<div class=\"flex-grow-1\">\r\n<h6 class=\"text-neutral-800 text-xl fw-medium mb-8\">تدريب ممتاز</h6>\r\n<p class=\"text-neutral-500 text-line-2\">من خلال دوراتنا التدريبية المنسقة والمحتوى التفاعلي</p>\r\n</div>\r\n</div>\r\n<div class=\"flex-align align-items-start gap-20 animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"800\"><span class=\"flex-shrink-0 w-60 h-60 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md\"><img class=\"animate__swing\" src=\"assets/images/icons/choose-us-icon2.png\" alt=\"\" /></span>\r\n<div class=\"flex-grow-1\">\r\n<h6 class=\"text-neutral-800 text-xl fw-medium mb-8\">عروض الدورات التدريبية</h6>\r\n<p class=\"text-neutral-500 text-line-2\">مسارات التعلم الشخصية، نحن نمكن المتعلمين من اكتساب</p>\r\n</div>\r\n</div>\r\n<div class=\"flex-align align-items-start gap-20 animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"1000\"><span class=\"flex-shrink-0 w-60 h-60 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md\"><img class=\"animate__swing\" src=\"assets/images/icons/choose-us-icon3.png\" alt=\"\" /></span>\r\n<div class=\"flex-grow-1\">\r\n<h6 class=\"text-neutral-800 text-xl fw-medium mb-8\">التعلم المبتكر</h6>\r\n<p class=\"text-neutral-500 text-line-2\">اندماج في التعلم المبتكر</p>\r\n</div>\r\n</div>\r\n</div>",
                'module' => 'about',
                'created_at' => '2025-08-11 21:34:53',
                'updated_at' => '2026-02-19 12:29:16',
            ],
            ['id' => 30, 'type' => 'text', 'label' => 'العنوان الأول', 'key' => 'address1', 'value' => 'السراج مول - مدينة مصر - القاهرة - مصر', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 31, 'type' => 'url', 'label' => 'رابط الخريطة للعنوان الأول', 'key' => 'address_map1', 'value' => 'https://www.google.com/maps/place/2B+Egypt+-+Head+Office/@30.0506816,31.3494222,15.75z', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 32, 'type' => 'text', 'label' => 'العنوان الثاني', 'key' => 'address2', 'value' => 'السراج مول - مدينة مصر - القاهرة - مصر', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 33, 'type' => 'url', 'label' => 'رابط الخريطة للعنوان الثاني', 'key' => 'address_map2', 'value' => 'https://www.google.com/maps/place/2B+Egypt+-+Head+Office/@30.0506816,31.3494222,15.75z', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 34, 'type' => 'text', 'label' => 'العنوان الثالث', 'key' => 'address3', 'value' => 'السراج مول - مدينة مصر - القاهرة - مصر', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 35, 'type' => 'url', 'label' => 'رابط الخريطة للعنوان الثالث', 'key' => 'address_map3', 'value' => 'https://www.google.com/maps/place/2B+Egypt+-+Head+Office/@30.0506816,31.3494222,15.75z', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 36, 'type' => 'number', 'label' => 'رقم الهاتف 1', 'key' => 'phone1', 'value' => '01111111111111', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 37, 'type' => 'number', 'label' => 'رقم الهاتف 2', 'key' => 'phone2', 'value' => '0252554645', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 38, 'type' => 'number', 'label' => 'رقم الواتساب', 'key' => 'whatsapp', 'value' => '015256148555', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 39, 'type' => 'text', 'label' => 'البريد الإلكتروني 1', 'key' => 'email1', 'value' => '2b@info.com', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 40, 'type' => 'text', 'label' => 'البريد الإلكتروني 2', 'key' => 'email2', 'value' => '2b@sales.com', 'module' => 'contact', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 41, 'type' => 'url', 'label' => 'رابط الفيسبوك', 'key' => 'facebook', 'value' => 'https://www.facebook.com/', 'module' => 'social', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 42, 'type' => 'url', 'label' => 'رابط تويتر', 'key' => 'twitter', 'value' => 'https://x.com/?lang=en&mx=2', 'module' => 'social', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 43, 'type' => 'url', 'label' => 'رابط اليوتيوب', 'key' => 'youtube', 'value' => 'https://www.youtube.com/', 'module' => 'social', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 44, 'type' => 'url', 'label' => 'رابط الأنستغرام', 'key' => 'instagram', 'value' => 'https://www.instagram.com/', 'module' => 'social', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 45, 'type' => 'url', 'label' => 'رابط لينكدان', 'key' => 'linkedin', 'value' => 'https://www.linkedin.com/', 'module' => 'social', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 46, 'type' => 'url', 'label' => 'رابط سناب شات', 'key' => 'snapchat', 'value' => 'https://www.linkedin.com/', 'module' => 'social', 'created_at' => '2025-08-11 21:34:53', 'updated_at' => '2025-08-12 10:33:23'],
            ['id' => 249, 'type' => 'number', 'label' => 'عدد الساعات في السنة', 'key' => 'yearly_hours', 'value' => '60', 'module' => 'settings', 'created_at' => '2026-02-19 12:28:58', 'updated_at' => '2026-02-19 12:29:17'],
            [
                // Row already exists in production (auto-created by
                // SettingRepository::updateByKey the first time an admin
                // saved the Settings page) under module=platform — target
                // that same key+module so this seeder stays idempotent
                // instead of forking a duplicate row. The value corrects a
                // placeholder default (the Angular form falls back several
                // unrelated numeric fields to 30) to a sane 1-5 rating
                // scale default.
                'type' => 'number',
                'label' => 'Abnormal Rating Threshold',
                'key' => 'abnormal_rating_threshold',
                'value' => '2',
                'module' => 'platform',
            ],
        ];
    }
}
