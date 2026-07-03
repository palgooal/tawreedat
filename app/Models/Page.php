<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'hero_title',
        'hero_description',
        'content',
        'seo_title',
        'seo_description',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            if (blank($page->slug)) {
                // Str::slug()'s default transliteration step strips Arabic characters
                // entirely; passing a null $language skips that step so Arabic titles
                // produce a real, non-empty slug instead of an empty string.
                $page->slug = Str::slug($page->title, '-', null);
            }
        });
    }

    /**
     * Only pages that are published and whose publish date has arrived
     * (or has no publish date restriction at all).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Mirrors scopePublished() for a single already-loaded model, so the
     * admin UI can tell whether the public "view" link will actually resolve.
     */
    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && ($this->published_at === null || $this->published_at->lessThanOrEqualTo(now()));
    }

    /**
     * The public-facing URL for this page. "about" and "plans" are fixed
     * slugs served by their own named routes; everything else goes through
     * the generic /p/{slug} route.
     */
    public function publicUrl(): string
    {
        return match ($this->slug) {
            'about' => route('about'),
            'plans' => route('plans'),
            default => route('pages.show', ['slug' => $this->slug]),
        };
    }
}
