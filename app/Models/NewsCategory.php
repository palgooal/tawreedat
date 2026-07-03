<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (NewsCategory $category) {
            if (blank($category->slug)) {
                // Str::slug()'s default transliteration step strips Arabic
                // characters entirely; a null $language skips that step so
                // Arabic names produce a real, non-empty slug.
                $category->slug = Str::slug($category->name, '-', null);
            }
        });
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
