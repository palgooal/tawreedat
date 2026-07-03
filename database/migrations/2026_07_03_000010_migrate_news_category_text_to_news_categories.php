<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill the new news_categories taxonomy from the legacy
     * news.category text column, then point each news row's
     * news_category_id at the matching category.
     *
     * The legacy `category` column is intentionally left in place
     * (untouched) for backward compatibility - see docs/ROADMAP.md
     * for the Phase 2 note to drop it once production data has been
     * migrated and verified.
     */
    public function up(): void
    {
        $distinctCategories = DB::table('news')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($distinctCategories as $categoryName) {
            $slug = Str::slug($categoryName, '-', null);

            $categoryId = DB::table('news_categories')->where('slug', $slug)->value('id');

            if (! $categoryId) {
                $categoryId = DB::table('news_categories')->insertGetId([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('news')
                ->where('category', $categoryName)
                ->whereNull('news_category_id')
                ->update(['news_category_id' => $categoryId]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Intentionally a no-op: this is a data migration, not a schema
     * change. Rolling it back would silently strip category
     * assignments that editors may have started relying on by then.
     * Auto-created news_categories rows can be removed manually via
     * the admin panel if truly needed.
     */
    public function down(): void
    {
        //
    }
};
