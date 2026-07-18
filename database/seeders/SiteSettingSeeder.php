<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Seed sensible defaults for every setting the admin Site Settings page
     * manages. Uses firstOrCreate (one row per key) rather than
     * SiteSetting::set(), so re-running db:seed never overwrites a value an
     * admin has already customised through Filament - it only fills in
     * keys that don't exist yet (e.g. after a deploy that added a new
     * setting field).
     *
     * robots_indexing_enabled defaults to false (disabled) here, since this
     * seeder is meant for local/dev environments. Enable it from
     * الإعدادات → إعدادات الموقع (or update this default) before the site
     * goes to production, otherwise /robots.txt will block all crawlers.
     */
    public function run(): void
    {
        $defaults = [
            'site_name' => ['value' => 'توريد', 'group' => 'site', 'type' => 'string'],
            'site_description' => [
                'value' => 'دليل توريد لشركات ومصانع مواد البناء والمقاولات في المملكة العربية السعودية.',
                'group' => 'site',
                'type' => 'string',
            ],
            'site_keywords' => [
                'value' => 'توريد مواد بناء، شركات مقاولات، موردين مواد بناء، السعودية',
                'group' => 'site',
                'type' => 'string',
            ],
            'contact_email' => ['value' => null, 'group' => 'site', 'type' => 'string'],
            'contact_phone' => ['value' => null, 'group' => 'site', 'type' => 'string'],
            'contact_address' => ['value' => null, 'group' => 'site', 'type' => 'string'],

            'default_seo_title' => [
                'value' => 'توريد | منصة ربط الشركات والموردين',
                'group' => 'seo',
                'type' => 'string',
            ],
            'default_seo_description' => [
                'value' => 'منصة سعودية تساعد الشركات والموردين والجهات الباحثة عن حلول التوريد على الوصول للمعلومة والتواصل بثقة.',
                'group' => 'seo',
                'type' => 'string',
            ],
            'google_analytics_id' => ['value' => null, 'group' => 'seo', 'type' => 'string'],
            'google_search_console_verification' => ['value' => null, 'group' => 'seo', 'type' => 'string'],

            'default_og_image' => ['value' => null, 'group' => 'og', 'type' => 'image'],

            'facebook_url' => ['value' => null, 'group' => 'social', 'type' => 'string'],
            'x_url' => ['value' => null, 'group' => 'social', 'type' => 'string'],
            'linkedin_url' => ['value' => null, 'group' => 'social', 'type' => 'string'],
            'instagram_url' => ['value' => null, 'group' => 'social', 'type' => 'string'],

            // false = "0" so SiteSetting::get()'s boolean cast reads it correctly.
            'robots_indexing_enabled' => ['value' => '0', 'group' => 'robots', 'type' => 'boolean'],
            'robots_extra_rules' => ['value' => null, 'group' => 'robots', 'type' => 'string'],
        ];

        foreach ($defaults as $key => $attributes) {
            SiteSetting::query()->firstOrCreate(['key' => $key], $attributes);
        }

        Cache::forget('site_settings.all');
    }
}
