<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the fields needed to convert an approved registration request
     * into a real Company record (see CompanyRegistrationRequestResource's
     * approve action). The old free-text `city`/`category` columns are kept
     * for backward compatibility with requests submitted before this
     * change - new submissions populate both the relationship columns and
     * the text columns (as a readable fallback/snapshot), but only the
     * relationship columns feed the created Company. See docs/DECISIONS.md.
     */
    public function up(): void
    {
        Schema::table('company_registration_requests', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('city')->constrained('cities')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->nullOnDelete();
            $table->string('logo')->nullable()->after('website');
            $table->foreignId('company_id')->nullable()->after('admin_notes')->constrained('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('company_registration_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn('logo');
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
