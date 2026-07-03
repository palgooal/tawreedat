<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    /**
     * The cache key holding every setting row, keyed by `key`. Kept as a
     * single cache entry (rather than one per setting) since the whole
     * table is small and is almost always read as a full set per request
     * (e.g. building <head> meta tags).
     */
    private const CACHE_KEY = 'site_settings.all';

    /**
     * Read a single setting value, cast according to its stored `type`.
     * Falls back to $default when the key doesn't exist yet (e.g. before
     * the settings seeder/admin form has ever saved it).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::allCached()->get($key);

        if ($setting === null) {
            return $default;
        }

        $value = $setting['value'];

        if ($value === null) {
            return $default;
        }

        return match ($setting['type']) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value,
        };
    }

    /**
     * Write a single setting value (creating the row if it doesn't exist
     * yet) and clear the settings cache so the next read sees fresh data.
     */
    public static function set(string $key, mixed $value, ?string $group = null, ?string $type = null): void
    {
        $stored = match (true) {
            is_bool($value) => $value ? '1' : '0',
            $value === null => null,
            default => (string) $value,
        };

        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $stored,
                'group' => $group,
                'type' => $type ?? (is_bool($value) ? 'boolean' : 'string'),
            ],
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * All settings rows, cached forever and keyed by `key` for O(1) lookups.
     * Cleared automatically by set() any time a setting is saved, so admins
     * always see their own changes reflected immediately.
     *
     * @return \Illuminate\Support\Collection<string, array{value: ?string, type: ?string}>
     */
    private static function allCached(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()
                ->get(['key', 'value', 'type'])
                ->keyBy('key')
                ->map(fn (self $setting) => [
                    'value' => $setting->value,
                    'type' => $setting->type,
                ]);
        });
    }
}
