<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Backfills sort_order from the current alphabetical order (the
     * implicit ordering every category page used before this change), in
     * steps of 10 - same convention as partner_logos - so a bare
     * `php artisan migrate` leaves every page looking exactly as it did
     * before, and admins can drag-reorder or type new values from there.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('type');
        });

        DB::table('categories')
            ->orderBy('name')
            ->pluck('id')
            ->each(fn ($id, $index) => DB::table('categories')->where('id', $id)->update([
                'sort_order' => ($index + 1) * 10,
            ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
