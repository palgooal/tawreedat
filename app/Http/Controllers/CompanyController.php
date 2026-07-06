<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class CompanyController extends Controller
{
    private const PER_PAGE = 12;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $citySlug = trim((string) $request->query('city', ''));
        $categorySlug = trim((string) $request->query('category', ''));
        $verifiedOnly = $request->boolean('verified');
        $featuredOnly = $request->boolean('featured');

        $query = Company::query()->where('status', 'active');

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhereHas('city', fn ($cityQuery) => $cityQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $activeCity = null;

        if ($citySlug !== '') {
            $activeCity = City::query()->where('slug', $citySlug)->first();

            // Filter by the relationship regardless of whether the slug
            // matched a real city, so an unknown/stale slug yields an empty
            // result set rather than silently showing everything.
            $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('slug', $citySlug));
        }

        $activeCategory = null;

        if ($categorySlug !== '') {
            $activeCategory = Category::query()->where('slug', $categorySlug)->first();

            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
        }

        if ($verifiedOnly) {
            $query->where('is_verified', true);
        }

        if ($featuredOnly) {
            $query->where('is_featured', true);
        }

        /** @var LengthAwarePaginator $companies */
        $companies = $query
            ->with(['city', 'category'])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Sidebar counts always reflect the full active-company set,
        // independent of the currently applied filters (a stable list
        // rather than one that shrinks as you filter), mirroring the same
        // convention NewsController uses for its category sidebar. Only
        // cities/categories that actually have at least one active company
        // are listed, so a filter link never leads to a guaranteed-empty
        // page.
        $cities = City::query()
            ->where('is_active', true)
            ->withCount(['companies' => fn ($inner) => $inner->where('status', 'active')])
            ->orderBy('name')
            ->get()
            ->filter(fn (City $city) => $city->companies_count > 0)
            ->values();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['companies' => fn ($inner) => $inner->where('status', 'active')])
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $category) => $category->companies_count > 0)
            ->values();

        $totalActiveCompanies = Company::query()->where('status', 'active')->count();
        $verifiedCompaniesCount = Company::query()->where('status', 'active')->where('is_verified', true)->count();
        $featuredCompaniesCount = Company::query()->where('status', 'active')->where('is_featured', true)->count();

        return view('companies.index', [
            'companies' => $companies,
            'cities' => $cities,
            'categories' => $categories,
            'totalActiveCompanies' => $totalActiveCompanies,
            'verifiedCompaniesCount' => $verifiedCompaniesCount,
            'featuredCompaniesCount' => $featuredCompaniesCount,
            'search' => $search,
            'activeCitySlug' => $citySlug,
            'activeCityName' => $activeCity?->name,
            'activeCategorySlug' => $categorySlug,
            'activeCategoryName' => $activeCategory?->name,
            'verifiedOnly' => $verifiedOnly,
            'featuredOnly' => $featuredOnly,
        ]);
    }
}
