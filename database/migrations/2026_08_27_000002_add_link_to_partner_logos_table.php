<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optional destination URL for a partner logo — set from Filament
     * (المحتوى → شعارات الشركاء). Nullable and defaulted to null so a bare
     * `php artisan migrate` leaves every existing logo exactly as it was
     * (plain, unlinked image) until an admin fills one in.
     */
    public function up(): void
    {
        Schema::table('partner_logos', function (Blueprint $table) {
            $table->string('link')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('partner_logos', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
