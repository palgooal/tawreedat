/**
 * Homepage Alpine component ("app").
 *
 * All server-generated data (companies, categories, cities, news, partner
 * logos, route URLs) is injected from resources/views/pages/home.blade.php
 * via `window.tawreedatHomeConfig` — a plain object built with Blade's
 * @json()/route() helpers — and passed in here as `config`. This module
 * itself contains no Blade syntax and no hardcoded Laravel URLs; every
 * route/data value it uses comes from that config bridge.
 *
 * Registered from resources/js/app.js inside the `alpine:init` listener,
 * which must run before Alpine's own CDN script calls Alpine.start() —
 * see the comment in resources/js/app.js for why that ordering matters.
 */
export function registerHomeApp(config) {
    Alpine.data('app', () => ({
        route: 'home',
        mobile: false,
        navStuck: false,
        heroSearch: '',
        heroCity: 'جميع المدن',
        selectedCategory: 'جميع الشركات',
        selectedCity: 'اختيار المدينة',
        sortBy: 'ترتيب افتراضي',
        companySearch: '',
        currentCompanyPage: 1,
        companiesPerPage: 9,
        // Measured height (px) of a real company-result card, used as the
        // placeholder slots' min-height (see missingCompanySlots below and
        // #companies-results in resources/views/pages/home.blade.php) so a
        // placeholder-only row can never be shorter than an actual card row.
        // 332 is only a pre-measurement fallback to avoid a zero-height
        // flash before init() gets its first real measurement -- it is not
        // relied on as the real value once the page has rendered.
        companyCardSlotHeight: 332,
        // ResizeObserver watching #companies-results so a viewport resize /
        // breakpoint change (which can change how many lines a card's title
        // or description wraps to, and therefore its real height) triggers
        // a re-measurement. Stored on the instance purely so destroy() can
        // disconnect it -- see init()/destroy() below.
        _companyCardResizeObserver: null,
        selectedNews: {},
        toast: '',
        toastTimer: null,
        // Single source of truth for the "Partner Logos" carousel below —
        // admin-managed via Filament (المحتوى → شعارات الشركاء) and queried
        // in HomeController; reaches here via the config bridge, not
        // hardcoded in this module.
        partnerLogos: config.partnerLogos,
        nav: config.nav,
        cities: config.cities,
        categories: config.categories,
        companies: config.companies,
        news: config.news,
        init() {
            this.route = this.routeFromPath();
            this.selectedNews = this.news[0];
            ['companySearch', 'selectedCity', 'selectedCategory', 'sortBy'].forEach(filter => {
                this.$watch(filter, () => this.currentCompanyPage = 1);
            });
            this.updateStickyNav();
            window.addEventListener('scroll', () => this.updateStickyNav(), {
                passive: true
            });
            window.addEventListener('resize', () => this.updateStickyNav());

            // Company-card slot height: measure once the initial render has
            // painted, then re-measure whenever the rendered set of cards
            // changes (pagination, filters -- paginatedCompanies already
            // reacts to both) or the viewport is resized in a way that
            // changes the grid's column count / wrapping.
            this.$nextTick(() => this.measureCompanyCardHeight());
            this.$watch('paginatedCompanies', () => {
                this.$nextTick(() => this.measureCompanyCardHeight());
            });

            const companiesGrid = document.getElementById('companies-results');
            if (companiesGrid && typeof ResizeObserver !== 'undefined') {
                this._companyCardResizeObserver = new ResizeObserver(() => {
                    this.measureCompanyCardHeight();
                });
                this._companyCardResizeObserver.observe(companiesGrid);
            }
        },
        // Alpine calls this automatically if/when this component's root
        // element is ever removed from the DOM. There's no such teardown in
        // normal use today (the root is <body>), but this keeps the
        // ResizeObserver from ever outliving its component regardless.
        destroy() {
            if (this._companyCardResizeObserver) {
                this._companyCardResizeObserver.disconnect();
                this._companyCardResizeObserver = null;
            }
        },
        // Reads the actual rendered height of the real company cards
        // currently in #companies-results and uses the tallest one as the
        // placeholder slot height, so a placeholder-only grid row can never
        // end up shorter than a real card row -- rather than guessing a
        // fixed px value that drifts out of sync with real content (title/
        // description line-wrapping) the moment card copy or font metrics
        // change. Deliberately reads only `.company-result-card` elements,
        // never placeholder slots, so this can't feed back into itself.
        measureCompanyCardHeight() {
            const cards = document.querySelectorAll('#companies-results .company-result-card');
            if (!cards.length) return;

            let max = 0;
            cards.forEach(card => {
                const height = card.getBoundingClientRect().height;
                if (height > max) max = height;
            });

            if (max > 0) this.companyCardSlotHeight = max;
        },
        updateStickyNav() {
            const nav = document.getElementById('site-sticky-nav');
            this.navStuck = window.scrollY >= ((nav?.offsetTop || 0) - 1);
        },
        routeFromPath() {
            const path = location.pathname;
            return config.routePathMap[path] || 'home';
        },
        openNews(item) {
            location.href = config.routes.newsShowTemplate.replace(
                '__SLUG__', encodeURIComponent(item.slug));
        },
        searchFromHero() {
            const params = new URLSearchParams();
            if (this.heroSearch.trim()) params.set('q', this.heroSearch.trim());
            if (this.heroCity !== 'جميع المدن') params.set('city', this.heroCity);
            location.href =
                `${config.routes.companiesIndex}${params.toString() ? `?${params.toString()}` : ''}`;
        },
        selectCategoryAndGo(category) {
            location.href =
                `${config.routes.companiesIndex}?sector=${encodeURIComponent(category)}`;
        },
        resetFilters() {
            this.selectedCategory = 'جميع الشركات';
            this.selectedCity = 'اختيار المدينة';
            this.sortBy = 'ترتيب افتراضي';
            this.companySearch = '';
            this.currentCompanyPage = 1;
        },
        setCompanyPage(page) {
            const nextPage = Math.min(Math.max(Number(page), 1), this.totalCompanyPages);
            if (!Number.isFinite(nextPage) || nextPage === this.currentCompanyPage) return;

            this.currentCompanyPage = nextPage;
        },
        showToast(message) {
            this.toast = message;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => this.toast = '', 3200);
        },
        get filteredCompanies() {
            const results = this.companies.filter(company => {
                const byCategory = this.selectedCategory === 'جميع الشركات' ||
                    company.category === this.selectedCategory;
                const byCity = this.selectedCity === 'اختيار المدينة' || this
                    .selectedCity === 'جميع المدن' || company.city === this
                    .selectedCity;
                const search = this.companySearch.trim();
                const bySearch = !search || company.name.includes(search);
                return byCategory && byCity && bySearch;
            });
            if (this.sortBy === 'حسب المدينة') return results.sort((a, b) => a.city
                .localeCompare(b.city, 'ar'));
            if (this.sortBy === 'الأحدث') return results.slice().reverse();
            return results;
        },
        get totalCompanyPages() {
            return Math.ceil(this.filteredCompanies.length / this.companiesPerPage);
        },
        get paginatedCompanies() {
            const lastPage = Math.max(this.totalCompanyPages, 1);
            const page = Math.min(this.currentCompanyPage, lastPage);
            const start = (page - 1) * this.companiesPerPage;
            return this.filteredCompanies.slice(start, start + this.companiesPerPage);
        },
        // Number of invisible placeholder grid slots needed to keep the
        // companies grid at a constant 9-slot footprint on every page, so a
        // shorter last page (fewer than companiesPerPage real cards) doesn't
        // shrink the grid's height and shift the pagination controls below
        // it -- see #companies-results in resources/views/pages/home.blade.php.
        // No placeholders in the empty-filter state (there's no grid to pad).
        get missingCompanySlots() {
            if (this.filteredCompanies.length === 0) return 0;
            return Math.max(this.companiesPerPage - this.paginatedCompanies.length, 0);
        },
        get companyPaginationItems() {
            const total = this.totalCompanyPages;
            const current = Math.min(this.currentCompanyPage, total);
            const page = number => ({ key: `page-${number}`, page: number });
            const gap = key => ({ key, page: null });

            if (total <= 7) {
                return Array.from({ length: total }, (_, index) => page(index + 1));
            }
            if (current <= 4) {
                return [1, 2, 3, 4, 5].map(page).concat(gap('gap-end'), page(total));
            }
            if (current >= total - 3) {
                return [page(1), gap('gap-start')].concat(
                    [total - 4, total - 3, total - 2, total - 1, total].map(page)
                );
            }
            return [page(1), gap('gap-start'), page(current - 1), page(current),
                page(current + 1), gap('gap-end'), page(total)
            ];
        }
    }));
}
