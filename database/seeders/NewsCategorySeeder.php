<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    /**
     * Seed the curated news taxonomy and re-point the demo news articles
     * (added by TawreedatDemoSeeder) at these categories.
     *
     * Safe to run multiple times: categories are matched by slug via
     * firstOrCreate, and news rows are matched by their existing slug.
     */
    public function run(): void
    {
        $categories = $this->seedCategories();
        $this->assignDemoNews($categories);
    }

    /**
     * @return array<string, int> category name => id
     */
    private function seedCategories(): array
    {
        $names = [
            'أخبار السوق',
            'المناقصات الحكومية',
            'المشاريع الكبرى',
            'أسعار مواد البناء',
            'تشريعات وأنظمة',
            'الاستدامة والابتكار',
        ];

        $ids = [];

        foreach ($names as $name) {
            $category = NewsCategory::firstOrCreate(
                ['slug' => Str::slug($name, '-', null)],
                ['name' => $name, 'is_active' => true],
            );

            $ids[$name] = $category->id;
        }

        return $ids;
    }

    /**
     * Maps each demo article from TawreedatDemoSeeder (identified by its
     * title-derived slug) to the curated category it fits best. Articles
     * are matched by slug rather than by their legacy `category` text,
     * since that legacy value is being phased out (see docs/ROADMAP.md).
     *
     * @param  array<string, int>  $categories  category name => id
     */
    private function assignDemoNews(array $categories): void
    {
        $assignments = [
            'تحديث أسعار مواد البناء لهذا الأسبوع' => 'أسعار مواد البناء',
            'ارتفاع الطلب على موردي الخرسانة الجاهزة في المدن الكبرى' => 'المشاريع الكبرى',
            'عروض جديدة من شركات الدهانات والتشطيبات' => 'أخبار السوق',
            'انضمام شركات جديدة إلى منصة توريد في قطاع الحديد' => 'أخبار السوق',
            'نصائح لاختيار مورد مواد البناء المناسب لمشروعك' => 'أخبار السوق',
            'توسع الطلب على حلول العزل المائي في المشاريع السكنية' => 'الاستدامة والابتكار',
            'نمو قطاع المقاولات في المملكة خلال العام الحالي' => 'المشاريع الكبرى',
        ];

        foreach ($assignments as $title => $categoryName) {
            $slug = Str::slug($title, '-', null);
            $categoryId = $categories[$categoryName] ?? null;

            if (! $categoryId) {
                continue;
            }

            News::query()
                ->where('slug', $slug)
                ->update(['news_category_id' => $categoryId]);
        }
    }
}
