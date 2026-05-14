<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the about-page singleton.
 *
 * Source rows from the legacy 2b dump only carried Arabic copy. We
 * preserve the original `_ar`/`_en` columns for backward compatibility
 * and additionally write JSON translations (matching the spatie
 * translatable layout used by App\Models\About).
 */
class AboutSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $aboutAr = "<ul class=\"list-unstyled who_we_are\">\r\n<li class=\"d-flex gap-10\">تُعد شركة تو بي من الشركات الرائدة في مجال تجارة التجزئة في مجال تكنولوجيا المعلومات والإلكترونيات في مصر وتشمل منتجاتنا أجهزة الكمبيوتر المحمولة، والهواتف المحمولة، والأجهزة اللوحية، وأجهزة الألعاب، والطابعات، والملحقات، وكاميرات المراقبة، والأجهزة المنزلية.</li>\r\n<li class=\"d-flex gap-10\">تأسست شركتنا عام ٢٠٠٠، وتعمل في هذا المجال منذ أكثر من ٢٠ عامًا في مصر، و١٠ سنوات في أسواق المملكة العربية السعودية.</li>\r\n<li class=\"d-flex gap-10\">تُعد تو بي من الشركات الرائدة في مصر في مجال تجارة التجزئة في مجال تكنولوجيا المعلومات والإلكترونيات.</li>\r\n<li class=\"d-flex gap-10\">تُصنف تو بي كأول شركة تجزئة في مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف المحمول، وخدمة مركز الاتصال.</li>\r\n<li class=\"d-flex gap-10\">يضم فريق عمل تو بي أكثر من ٦٠٠ موظف، يبذلون قصارى جهدهم لتلبية متطلبات العملاء واحتياجاتهم.</li>\r\n</ul>";
        $aboutEn = "<ul class=\"list-unstyled who_we_are\">\r\n<li class=\"d-flex gap-10\">2B is one of the leading IT &amp; consumer-electronics retailers in Egypt. Our portfolio covers laptops, smartphones, tablets, gaming consoles, printers, accessories, surveillance cameras and home appliances.</li>\r\n<li class=\"d-flex gap-10\">Founded in 2000, we have been operating for more than 20 years in Egypt and for 10 years across the Saudi Arabian markets.</li>\r\n<li class=\"d-flex gap-10\">2B is recognised as one of the top retailers in Egypt for information technology and electronics.</li>\r\n<li class=\"d-flex gap-10\">2B is classified as the first IT retailer in Egypt. We currently operate 60 stores across the country, alongside our e-commerce website, mobile application and call-centre service.</li>\r\n<li class=\"d-flex gap-10\">Our 2B team includes more than 600 employees, all dedicated to meeting customer requirements and needs.</li>\r\n</ul>";

        $missionAr = "<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"200\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\r\n</div>\r\n</div>";
        $missionEn = "<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"200\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">A leader in Egypt's IT sector. We currently run 60 stores across the country, plus our e-commerce site and mobile app.</p>\r\n</div>\r\n</div>";

        $visionAr = "<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\r\n</div>\r\n</div>";
        $visionEn = "<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">To remain a benchmark IT retailer in Egypt — operating 60+ stores, an e-commerce platform, and a mobile app.</p>\r\n</div>\r\n</div>";

        $goalsAr = "<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\r\n</div>\r\n</div>";
        $goalsEn = "<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">Grow Egypt's IT retail leadership across 60+ branches, our e-commerce site and our mobile app.</p>\r\n</div>\r\n</div>";

        $row = [
            'id' => 1,
            'about_en' => $aboutEn,
            'about_ar' => $aboutAr,
            'mission_en' => $missionEn,
            'mission_ar' => $missionAr,
            'vision_en' => $visionEn,
            'vision_ar' => $visionAr,
            'goals_en' => $goalsEn,
            'goals_ar' => $goalsAr,
            'about' => $this->json($aboutAr, $aboutEn),
            'mission' => $this->json($missionAr, $missionEn),
            'vision' => $this->json($visionAr, $visionEn),
            'goals' => $this->json($goalsAr, $goalsEn),
            'image' => 'About/mUeo9KML5KhLGK0lxnrQe7db34e9dda22fd6cb6fcc47c9fbee9e.png',
            'created_at' => '2025-08-07 13:27:30',
            'updated_at' => '2025-08-12 12:11:42',
        ];

        $this->schemaAwareUpsert('abouts', [$row], ['id'], array_keys($row));
    }

    private function json(string $ar, string $en): string
    {
        return json_encode(
            ['ar' => $ar, 'en' => $en],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
