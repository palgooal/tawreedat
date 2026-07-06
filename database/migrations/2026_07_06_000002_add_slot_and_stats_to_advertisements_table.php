<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            // Nullable + no FK cascade-delete: an admin deleting a slot must
            // not silently destroy advertisement rows. The legacy `position`
            // column is intentionally left in place (see step 3 migration
            // and DECISIONS.md) so nothing reading it directly breaks.
            $table->foreignId('advertisement_slot_id')
                ->nullable()
                ->after('position')
                ->constrained('advertisement_slots')
                ->nullOnDelete();

            $table->unsignedInteger('priority')->default(0)->after('is_active');
            $table->unsignedBigInteger('views')->default(0)->after('priority');
            $table->unsignedBigInteger('clicks')->default(0)->after('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('advertisement_slot_id');
            $table->dropColumn(['priority', 'views', 'clicks']);
        });
    }
};
