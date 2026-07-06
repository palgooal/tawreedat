<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use App\Models\AdvertisementSlot;
use App\Support\Permissions;
use Filament\Widgets\Widget;

/**
 * Dashboard snapshot of the slot-based advertising system: which of the six
 * known slots currently have an active advertisement serving, plus overall
 * click/impression totals. Every value is a genuine runtime query against
 * AdvertisementSlot/Advertisement — nothing here is hardcoded.
 */
class AdvertisingStatusWidget extends Widget
{
    protected static ?int $sort = -8;

    protected int | string | array $columnSpan = 1;

    protected string $view = 'filament.widgets.advertising-status-widget';

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can(Permissions::MANAGE_SETTINGS);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $slots = AdvertisementSlot::query()
            ->withCount(['advertisements as active_advertisements_count' => function ($query) {
                $query->active();
            }])
            ->orderBy('id')
            ->get()
            ->map(fn (AdvertisementSlot $slot) => [
                'name' => $slot->name,
                'key' => $slot->key,
                'hasActive' => $slot->active_advertisements_count > 0,
            ]);

        return [
            'slots' => $slots,
            'totalActiveAds' => Advertisement::query()->active()->count(),
            'totalClicks' => (int) Advertisement::query()->sum('clicks'),
            'totalImpressions' => (int) Advertisement::query()->sum('views'),
        ];
    }
}
