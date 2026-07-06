<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'link',
        // `position` is kept only for backward compatibility — see
        // DECISIONS.md. New code should read/write `advertisement_slot_id`
        // (via the `slot` relationship) instead.
        'position',
        'advertisement_slot_id',
        'starts_at',
        'ends_at',
        'is_active',
        'priority',
        'views',
        'clicks',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'priority' => 'integer',
            'views' => 'integer',
            'clicks' => 'integer',
        ];
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AdvertisementSlot::class, 'advertisement_slot_id');
    }

    /**
     * Click-through rate as a percentage, rounded to 2 decimals. Null (not
     * 0) when there have been no impressions yet, so the UI can show
     * "—" instead of a misleading "0%".
     */
    public function getCtrAttribute(): ?float
    {
        if ($this->views === 0) {
            return null;
        }

        return round(($this->clicks / $this->views) * 100, 2);
    }

    /**
     * Only ads that are switched on and currently within their date window
     * (if one is set).
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    /**
     * @deprecated Kept only so any leftover callers against the legacy
     * `position` column keep working. Prefer scopeForSlot().
     */
    public function scopeForPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }

    /**
     * Ads assigned to a given slot, looked up by the slot's stable `key`
     * (e.g. "header_banner") rather than its numeric id. Also requires the
     * slot itself to be active — a slot toggled off in the admin panel
     * should stop rendering immediately, even if an otherwise-active ad is
     * still linked to it.
     */
    public function scopeForSlot(Builder $query, string $slotKey): Builder
    {
        return $query->whereHas(
            'slot',
            fn (Builder $q) => $q->where('key', $slotKey)->where('is_active', true)
        );
    }
}
