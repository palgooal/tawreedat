<?php

namespace App\Support;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;

/**
 * Single lookup point for "which advertisement should render in slot X right
 * now". Every consumer (view composers, the ads click redirect, the
 * dashboard widget) goes through here instead of querying the
 * Advertisement model directly, so slot resolution and caching stay in one
 * place.
 */
class AdvertisementManager
{
    private const CACHE_TTL_MINUTES = 5;

    /**
     * The latest active advertisement assigned to the given slot key (e.g.
     * "header_banner"), or null if none is currently active. Result is
     * cached for 5 minutes per slot so rendering a banner never costs a
     * query on every request.
     */
    public static function slot(string $slotKey): ?Advertisement
    {
        return Cache::remember(
            self::cacheKey($slotKey),
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => Advertisement::query()
                ->active()
                ->forSlot($slotKey)
                ->orderByDesc('priority')
                ->latest()
                ->first()
        );
    }

    /**
     * Forget the cached result for a slot (or every slot when $slotKey is
     * null). Call this from the Filament resource's save/delete hooks if
     * "changes should show up within 5 minutes" isn't good enough for a
     * given workflow — not wired up automatically to keep this class
     * dependency-free.
     */
    public static function forget(?string $slotKey = null): void
    {
        if ($slotKey !== null) {
            Cache::forget(self::cacheKey($slotKey));

            return;
        }

        foreach (self::knownSlotKeys() as $key) {
            Cache::forget(self::cacheKey($key));
        }
    }

    /**
     * @return array<int, string>
     */
    public static function knownSlotKeys(): array
    {
        return [
            'header_banner',
            'home_banner_1',
            'home_banner_2',
            'home_banner_3',
            'news_sidebar',
            'news_footer',
        ];
    }

    private static function cacheKey(string $slotKey): string
    {
        return "advertisement_manager.slot.{$slotKey}";
    }
}
