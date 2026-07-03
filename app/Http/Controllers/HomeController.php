<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * A small rotating icon palette used to keep the category cards visually
     * consistent, since categories don't store their own icon in the database.
     *
     * @var array<int, string>
     */
    private const CATEGORY_ICONS = ['▦', '◼', '▤', '✦', '◒', '⚙', '◆', '❖', '▣', '❋'];

    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['companies' => fn ($query) => $query->where('status', 'active')])
            ->orderBy('name')
            ->get()
            ->values()
            ->map(fn (Category $category, int $index) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => self::CATEGORY_ICONS[$index % count(self::CATEGORY_ICONS)],
                'count' => $category->companies_count,
            ])
            ->all();

        $companiesQuery = Company::query()
            ->where('status', 'active')
            ->with(['city', 'category']);

        $companiesCount = (clone $companiesQuery)->count();
        $verifiedCompaniesCount = (clone $companiesQuery)->where('is_verified', true)->count();

        $companies = $companiesQuery
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')
            ->latest()
            ->get()
            ->map(fn (Company $company) => [
                'name' => $company->name,
                'slug' => $company->slug,
                'logo' => $company->logo ? Storage::disk('public')->url($company->logo) : null,
                'category' => $company->category?->name,
                'city' => $company->city?->name,
                'desc' => $company->description,
                'website' => $company->website,
                'isFeatured' => (bool) $company->is_featured,
                'isVerified' => (bool) $company->is_verified,
            ])
            ->all();

        $cities = City::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $citiesNames = $cities->pluck('name')->all();

        $news = News::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get()
            ->map(fn (News $item) => [
                'slug' => $item->slug,
                'category' => $item->category,
                'title' => $item->title,
                'time' => $item->published_at?->locale('ar')->diffForHumans(),
                'image' => $item->image ? Storage::disk('public')->url($item->image) : null,
            ])
            ->all();

        $now = now();

        $activeAds = Advertisement::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->latest()
            ->get()
            ->keyBy('position');

        return view('pages.home', [
            'dbCategories' => $categories,
            'dbCompanies' => $companies,
            'dbCities' => $citiesNames,
            'dbNews' => $news,
            'categoriesCount' => count($categories),
            'companiesCount' => $companiesCount,
            'citiesCount' => $cities->count(),
            'verifiedCompaniesCount' => $verifiedCompaniesCount,
            'adHome' => $activeAds->get('home'),
            'adHeader' => $activeAds->get('header'),
            'adSidebar' => $activeAds->get('sidebar'),
        ]);
    }
}
