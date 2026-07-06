<?php

namespace Database\Seeders;

use App\Models\AdvertisementSlot;
use Illuminate\Database\Seeder;

/**
 * Mirrors the seed step already performed inside
 * 2026_07_06_000001_create_advertisement_slots_table.php, so that running
 * `php artisan db:seed` on its own (e.g. after a fresh `migrate:fresh`) still
 * leaves every slot in place. Uses updateOrCreate so re-running it is a
 * no-op for slots that already exist and never duplicates rows.
 */
class AdvertisementSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            [
                'key' => 'header_banner',
                'name' => 'بانر أعلى الموقع',
                'description' => 'يظهر أعلى كل صفحات الموقع، أسفل الشريط العلوي مباشرة.',
                'width' => 960,
                'height' => 154,
            ],
            [
                'key' => 'home_banner_1',
                'name' => 'إعلان الصفحة الرئيسية (1)',
                'description' => 'المساحة الإعلانية الرئيسية الكبيرة ضمن قسم "مساحات إعلانية" بالصفحة الرئيسية.',
                'width' => 1600,
                'height' => 900,
            ],
            [
                'key' => 'home_banner_2',
                'name' => 'إعلان الصفحة الرئيسية (2)',
                'description' => 'أول بطاقة إعلانية ضمن قسم "مساحات إعلانية" بالصفحة الرئيسية.',
                'width' => 1600,
                'height' => 900,
            ],
            [
                'key' => 'home_banner_3',
                'name' => 'إعلان الصفحة الرئيسية (3)',
                'description' => 'ثاني بطاقة إعلانية ضمن قسم "مساحات إعلانية" بالصفحة الرئيسية.',
                'width' => 320,
                'height' => 213,
            ],
            [
                'key' => 'news_sidebar',
                'name' => 'الشريط الجانبي للأخبار',
                'description' => 'يظهر في الشريط الجانبي لصفحة تفاصيل الخبر.',
                'width' => 320,
                'height' => 480,
            ],
            [
                'key' => 'news_footer',
                'name' => 'أسفل الخبر',
                'description' => 'يظهر أسفل محتوى الخبر في صفحة تفاصيل الخبر.',
                'width' => 728,
                'height' => 200,
            ],
        ];

        foreach ($slots as $slot) {
            AdvertisementSlot::query()->updateOrCreate(
                ['key' => $slot['key']],
                [...$slot, 'is_active' => true],
            );
        }
    }
}
