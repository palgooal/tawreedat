<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'category',
        'news_category_id',
        'published_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (News $news) {
            if (blank($news->slug)) {
                $news->slug = Str::slug($news->title, '-', null);
            }
        });
    }

    /**
     * Only news that is published, has a publish date, and that publish
     * date has already arrived. Unlike Page::scopePublished(), a null
     * published_at does NOT count as visible here (news must be dated).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * The real taxonomy relationship. Named categoryRelation() (rather than
     * category()) because the legacy `category` text column/attribute is
     * still present on this model for backward compatibility - see
     * docs/ROADMAP.md for the Phase 2 note to drop that column and rename
     * this to category() once production data has fully migrated.
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /**
     * Mirrors scopePublished() for a single already-loaded model, so the
     * admin UI can tell whether the public "view" link will actually resolve.
     */
    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now());
    }

    /**
     * The public-facing URL for this article.
     */
    public function publicUrl(): string
    {
        return route('news.show', ['slug' => $this->slug]);
    }
}
