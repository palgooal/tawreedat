<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Legacy `position` value => new slot `key`.
     *
     * `sidebar` intentionally moves to `news_sidebar` (not a home_banner_*
     * slot) and `footer` to `home_banner_3` — see DECISIONS.md for why the
     * old "header"/"sidebar" position values, which were being reused
     * ambiguously between the sitewide banner and homepage cards, are being
     * split into distinct, unambiguous slots.
     *
     * @var array<string, string>
     */
    public static array $map = [
        'header' => 'header_banner',
        'home' => 'home_banner_1',
        'footer' => 'home_banner_3',
        'sidebar' => 'news_sidebar',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $slotIdsByKey = DB::table('advertisement_slots')
            ->pluck('id', 'key');

        foreach (self::$map as $position => $slotKey) {
            $slotId = $slotIdsByKey[$slotKey] ?? null;

            if ($slotId === null) {
                // Slot table wasn't seeded as expected — skip rather than
                // fail the whole migration, so a partial environment can
                // still be inspected and re-run safely.
                continue;
            }

            // Only touch rows that haven't already been assigned a slot, so
            // this migration is safe to run more than once and never
            // clobbers a manual re-assignment made from the admin panel.
            DB::table('advertisements')
                ->where('position', $position)
                ->whereNull('advertisement_slot_id')
                ->update(['advertisement_slot_id' => $slotId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slotKeys = array_values(self::$map);

        $slotIds = DB::table('advertisement_slots')
            ->whereIn('key', $slotKeys)
            ->pluck('id');

        // Best-effort revert: clear slot assignments this migration made.
        // Position column was never touched, so no data is lost.
        DB::table('advertisements')
            ->whereIn('advertisement_slot_id', $slotIds)
            ->update(['advertisement_slot_id' => null]);
    }
};
