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
            ->ordered()
            ->get()
            ->filter(fn (Category $category) => $category->companies_count > 0)
            ->values();

        $totalActiveCompanies = Company::query()->where('status', 'active')->count();
        $verifiedCompaniesCount = Company::query()->where('status', 'active')->where('is_verified', true)->count();
        $featuredCompaniesCount = Company::query()->where('status', 'active')->where('is_featured', true)->count();

        // Number of invisible placeholder grid slots the results partial
        // should render after the real cards, so a shorter last page (or
        // any page with fewer than PER_PAGE matches) doesn't shrink the
        // results grid and shift the pagination nav below it -- see
        // resources/views/companies/partials/results.blade.php and the
        // client-side height stabilization in resources/js/pages/companies.js.
        // $companies->count() is the number of items on THIS page (not the
        // total across all pages); guarded to 0 when there are no matching
        // companies at all, since an empty state has no grid to pad.
        $missingCompanySlots = $companies->total() > 0
            ? max(self::PER_PAGE - $companies->count(), 0)
            : 0;

        $viewData = [
            'companies' => $companies,
            'missingCompanySlots' => $missingCompanySlots,
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
        ];

        // Pagination/filtering on this page is progressively enhanced:
        // resources/js/pages/companies.js intercepts pagination-link and
        // filter-form submissions and re-requests this same route via
        // fetch(), which sends the X-Requested-With header Request::ajax()
        // checks for. On an AJAX request we skip the full layout (header,
        // hero, footer, sidebar) and return only the results partial that
        // JS swaps into #companies-directory-results -- the exact same
        // partial companies.index itself includes, so the two paths can
        // never render different markup for the same filters/page. A
        // normal browser navigation (no JS, or the fetch failing and
        // falling back to a real link) never sends that header and always
        // gets the full page, so nothing here removes the plain-links
        // fallback the route already had.
        if ($request->ajax()) {
            return view('companies.partials.results', $viewData);
        }

        return view('companies.index', $viewData);
    }
}
