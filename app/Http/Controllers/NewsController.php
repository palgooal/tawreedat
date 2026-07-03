<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class NewsController extends Controller
{
    private const PER_PAGE = 9;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('category', ''));

        $query = News::query()->published();

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $activeCategory = null;

        if ($categorySlug !== '') {
            $activeCategory = NewsCategory::query()->where('slug', $categorySlug)->first();

            // Filter by the relationship regardless of whether the slug
            // matched a real category, so an unknown/stale slug yields an
            // empty result set rather than silently showing everything.
            $query->whereHas('categoryRelation', function ($inner) use ($categorySlug) {
                $inner->where('slug', $categorySlug);
            });
        }

        /** @var LengthAwarePaginator $news */
        $news = $query
            ->with('categoryRelation')
            ->orderByDesc('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Category counts always reflect the full published set, independent
        // of the currently applied search/category filter (a stable sidebar
        // taxonomy rather than a "counts shrink as you filter" list). Only
        // categories that actually have published news are listed, so the
        // sidebar never links to a guaranteed-empty page.
        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->withCount(['news' => fn ($inner) => $inner->published()])
            ->orderBy('name')
            ->get()
            ->filter(fn (NewsCategory $category) => $category->news_count > 0)
            ->values();

        $totalPublished = News::query()->published()->count();

        return view('news.index', [
            'news' => $news,
            'categories' => $categories,
            'totalPublished' => $totalPublished,
            'search' => $search,
            'activeCategorySlug' => $categorySlug,
            'activeCategoryName' => $activeCategory?->name,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $news = News::query()
            ->published()
            ->where('slug', $slug)
            ->with('categoryRelation')
            ->firstOr(function () {
                abort(404);
            });

        $relatedQuery = News::query()
            ->published()
            ->with('categoryRelation')
            ->where('id', '!=', $news->id);

        if ($news->news_category_id) {
            $relatedQuery->where('news_category_id', $news->news_category_id);
        }

        $relatedNews = $relatedQuery
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->withCount(['news' => fn ($inner) => $inner->published()])
            ->orderBy('name')
            ->get()
            ->filter(fn (NewsCategory $category) => $category->news_count > 0)
            ->values()
            ->take(6);

        return view('news.show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
            'categories' => $categories,
            'backUrl' => $this->safeBackUrl($request->query('from')) ?? route('news.index'),
        ]);
    }

    /**
     * Only accept a same-site relative path (e.g. "/news?category=...").
     * Rejects protocol-relative ("//evil.com"), scheme-prefixed
     * ("/javascript:...") or any absolute/external URL.
     */
    private function safeBackUrl(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = trim($value);

        if (! str_starts_with($value, '/') || str_starts_with($value, '//')) {
            return null;
        }

        if (preg_match('#^/[a-z][a-z0-9+.-]*:#i', $value) === 1) {
            return null;
        }

        return $value;
    }
}
