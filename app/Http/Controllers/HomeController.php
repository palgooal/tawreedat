<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\News;
use App\Models\PartnerLogo;
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

        // Admin-managed via Filament (المحتوى → شعارات الشركاء), replacing
        // what used to be a hardcoded array in this view's Alpine data.
        $partnerLogos = PartnerLogo::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (PartnerLogo $partnerLogo) => [
                'name' => $partnerLogo->name,
                'logo' => $partnerLogo->logo ? Storage::disk('public')->url($partnerLogo->logo) : null,
            ])
            ->all();

        // Advertisement data (headerBanner, homeBanner1/2/3) is injected by
        // the 'pages.home' view composer in AppServiceProvider, via
        // App\Support\AdvertisementManager — not queried here, so this
        // controller doesn't need to know about slots at all.
        return view('pages.home', [
            'dbCategories' => $categories,
            'dbCompanies' => $companies,
            'dbCities' => $citiesNames,
            'dbNews' => $news,
            'dbPartnerLogos' => $partnerLogos,
            'categoriesCount' => count($categories),
            'companiesCount' => $companiesCount,
            'citiesCount' => $cities->count(),
            'verifiedCompaniesCount' => $verifiedCompaniesCount,
        ]);
    }
}
