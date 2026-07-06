@php($seoTitle = 'دليل الشركات والموردين | توريدات')
@php($seoDescription = 'استعرض الشركات والموردين المسجلين في منصة توريدات حسب المدينة والتصنيف وحالة التوثيق.')
@extends('layouts.app', ['alpineComponent' => 'companiesPage'])

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/images/hero-construction-1200.jpg') }}" fetchpriority="high">
@endpush

@section('content')

    <!-- Page Hero -->
    <section class="relative flex min-h-[320px] items-center overflow-hidden bg-gov-950 py-14 text-white sm:py-16">
        <img src="{{ asset('assets/images/hero-construction-1200.jpg') }}"
            srcset="{{ asset('assets/images/hero-construction-768.jpg') }} 768w, {{ asset('assets/images/hero-construction-1200.jpg') }} 1200w, {{ asset('assets/images/hero-construction.jpg') }} 1600w"
            sizes="100vw" alt="" width="1600" height="900" decoding="async" fetchpriority="high"
            class="absolute inset-0 h-full w-full object-cover opacity-25">
        <div
            class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,50,42,.95),rgba(0,58,48,.88),rgba(0,50,42,.95))]">
        </div>
        <div class="relative mx-auto w-full max-w-[1500px] px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="flex items-center gap-2 text-xs font-bold text-gold-200">
                    <a href="{{ route('home') }}" class="transition hover:text-white">الرئيسية</a>
                    <span class="text-white/40">/</span>
                    <span>الشركات</span>
                </div>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight sm:text-5xl">دليل الشركات والموردين</h1>
                <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-100 sm:text-base">
                    استعرض الشركات والموردين المسجلين في توريدات حسب المدينة أو التصنيف أو حالة التوثيق.
                </p>
            </div>
        </div>
    </section>

    <!-- Directory -->
    <section class="pb-16 pt-6 [contain-intrinsic-size:900px] [content-visibility:auto]">
        <div class="mx-auto grid max-w-[1500px] gap-8 px-4 sm:px-6 lg:grid-cols-[300px_1fr] lg:px-8">

            <!-- Content (right/main in RTL) -->
            <div class="lg:col-start-2 lg:row-start-1">
                <div
                    class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="leading-7 text-slate-700">
                        <span>عدد الشركات المطابقة:</span>
                        <span class="font-extrabold text-gov-950">{{ $companies->total() }}</span>
                        @if ($activeCategoryName)
                            <span>
                                <span>ضمن</span>
                                <strong class="font-bold text-gov-900">{{ $activeCategoryName }}</strong>
                            </span>
                        @endif
                        @if ($activeCityName)
                            <span>
                                <span>في</span>
                                <strong class="font-bold text-gov-900">{{ $activeCityName }}</strong>
                            </span>
                        @endif
                        @if ($search !== '')
                            <span>
                                <span>عن</span>
                                <strong class="font-bold text-gov-900">"{{ $search }}"</strong>
                            </span>
                        @endif
                    </p>
                    @if ($search !== '' || $activeCitySlug !== '' || $activeCategorySlug !== '' || $verifiedOnly || $featuredOnly)
                        <a href="{{ route('companies.index') }}"
                            class="self-start rounded-xl border border-gov-100 bg-gov-50 px-4 py-2 text-xs font-bold text-gov-900 transition hover:border-gov-200 hover:bg-gov-100 sm:self-auto">
                            عرض كل الشركات
                        </a>
                    @endif
                </div>

                <p id="companies-results-status" class="sr-only" aria-live="polite">
                    عدد الشركات المطابقة: {{ $companies->total() }}
                    @if ($activeCategoryName) ضمن {{ $activeCategoryName }} @endif
                    @if ($activeCityName) في {{ $activeCityName }} @endif
                    @if ($search !== '') عن "{{ $search }}" @endif
                </p>

                @if ($companies->isNotEmpty())
                    <div class="grid grid-cols-1 items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($companies as $company)
                            @php($logoUrl = $company->logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo) : asset('assets/images/company-placeholder.png'))
                            <article
                                class="group relative flex min-h-[332px] flex-col rounded-3xl border border-slate-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-gov-200 hover:shadow-[0_12px_28px_rgba(7,30,23,0.10)]">
                                @if ($company->is_featured)
                                    <span
                                        class="absolute end-4 top-4 inline-flex items-center gap-1 rounded-full bg-gold-500 px-2.5 py-1 text-[10px] font-bold text-white shadow">
                                        مميزة
                                    </span>
                                @endif

                                @if ($company->is_verified)
                                    <span title="شركة موثقة"
                                        class="absolute start-4 top-4 inline-flex h-6 w-6 items-center justify-center rounded-full bg-gov-50 text-gov-700 shadow-sm ring-1 ring-gov-100">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.4" aria-hidden="true">
                                            <path d="M9 12.5 11 14.5 15.5 9.5" />
                                        </svg>
                                        <span class="sr-only">شركة موثقة</span>
                                    </span>
                                @endif

                                <div
                                    class="mx-auto flex h-[92px] w-[92px] items-center justify-center rounded-3xl border border-slate-200 bg-white p-4 transition duration-300 group-hover:border-gov-100">
                                    <img src="{{ $logoUrl }}" alt="{{ $company->name }}" width="92" height="92"
                                        loading="lazy" decoding="async" class="h-full w-full object-contain object-center">
                                </div>

                                <h3
                                    class="mx-auto mt-5 line-clamp-2 min-h-10 max-w-[250px] text-[18px] font-extrabold leading-7 text-gov-950">
                                    {{ $company->name }}
                                </h3>

                                <div class="mt-3 flex items-center justify-center gap-2 text-[12px] font-semibold text-slate-500">
                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <path d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>
                                    <span>{{ $company->city?->name }}</span>
                                    <span class="h-1 w-1 rounded-full bg-slate-300" aria-hidden="true"></span>
                                    <span>{{ $company->category?->name }}</span>
                                </div>

                                <p class="mx-auto mt-3 line-clamp-2 min-h-12 max-w-[280px] text-[13px] leading-6 text-slate-500">
                                    {{ $company->description }}
                                </p>

                                <div class="mt-auto pt-6">
                                    @if ($company->website)
                                        <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer"
                                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-gov-800 text-sm font-bold text-white shadow-[0_14px_28px_rgba(7,30,23,0.18)] transition hover:bg-gov-900 hover:shadow-[0_16px_32px_rgba(7,30,23,0.24)]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                aria-hidden="true">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                            </svg>
                                            <span>زيارة الموقع الإلكتروني</span>
                                        </a>
                                    @else
                                        <span aria-disabled="true"
                                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-200 text-sm font-bold text-slate-500">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                aria-hidden="true">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                            </svg>
                                            <span>الموقع الإلكتروني غير متاح</span>
                                        </span>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-14 text-center">
                        <h3 class="font-extrabold text-gov-900">لا توجد شركات مطابقة لهذه المعايير.</h3>
                        <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-600">
                            جرّب تغيير المدينة أو التصنيف أو كلمات البحث.
                        </p>
                        <a href="{{ route('companies.index') }}"
                            class="mt-5 inline-flex rounded-xl bg-gov-800 px-5 py-3 text-xs font-bold text-white">مسح الفلاتر</a>
                    </div>
                @endif

                @if ($companies->hasPages())
                    <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="ترقيم صفحات الشركات">
                        @if ($companies->onFirstPage())
                            <span class="inline-flex h-11 min-w-24 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-400 opacity-45">السابق</span>
                        @else
                            <a href="{{ $companies->previousPageUrl() }}"
                                class="inline-flex h-11 min-w-24 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">السابق</a>
                        @endif

                        @foreach ($companies->getUrlRange(1, $companies->lastPage()) as $page => $url)
                            @if ($page === $companies->currentPage())
                                <span aria-current="page"
                                    class="grid h-11 w-11 place-items-center rounded-2xl bg-gov-800 text-sm font-bold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 transition hover:border-gov-300">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($companies->hasMorePages())
                            <a href="{{ $companies->nextPageUrl() }}"
                                class="inline-flex h-11 min-w-24 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">التالي</a>
                        @else
                            <span class="inline-flex h-11 min-w-24 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-400 opacity-45">التالي</span>
                        @endif
                    </nav>
                @endif
            </div>

            <!-- Filters (left/sidebar in RTL) -->
            <aside class="space-y-5 lg:col-start-1 lg:row-start-1 lg:sticky lg:top-24 lg:self-start">
                <form method="GET" action="{{ route('companies.index') }}"
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5">
                    <h2 class="font-extrabold text-gov-950">تصفية النتائج</h2>

                    <div>
                        <label for="company-search" class="mb-1.5 block text-xs font-bold text-gov-950">البحث</label>
                        <input id="company-search" name="q" type="search" value="{{ $search }}"
                            aria-describedby="companies-results-status"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-gold-400 focus:bg-white"
                            placeholder="اسم الشركة، الوصف، الهاتف...">
                    </div>

                    <div>
                        <label for="filter-city" class="mb-1.5 block text-xs font-bold text-gov-950">المدينة</label>
                        <select id="filter-city" name="city"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none transition focus:border-gold-400 focus:bg-white">
                            <option value="">كل المدن</option>
                            @foreach ($cities as $cityRow)
                                <option value="{{ $cityRow->slug }}" @selected($activeCitySlug === $cityRow->slug)>
                                    {{ $cityRow->name }} ({{ $cityRow->companies_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-category" class="mb-1.5 block text-xs font-bold text-gov-950">التصنيف</label>
                        <select id="filter-category" name="category"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none transition focus:border-gold-400 focus:bg-white">
                            <option value="">كل التصنيفات</option>
                            @foreach ($categories as $categoryRow)
                                <option value="{{ $categoryRow->slug }}" @selected($activeCategorySlug === $categoryRow->slug)>
                                    {{ $categoryRow->name }} ({{ $categoryRow->companies_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2 border-t border-slate-100 pt-4">
                        <label class="flex items-center gap-2.5 text-sm font-semibold text-gov-900">
                            <input type="checkbox" name="verified" value="1" @checked($verifiedOnly)
                                class="h-4 w-4 rounded border-slate-300 text-gov-800 focus:ring-gold-400">
                            شركات موثقة فقط
                            <span class="text-xs font-normal text-slate-500">({{ $verifiedCompaniesCount }})</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-sm font-semibold text-gov-900">
                            <input type="checkbox" name="featured" value="1" @checked($featuredOnly)
                                class="h-4 w-4 rounded border-slate-300 text-gov-800 focus:ring-gold-400">
                            شركات مميزة فقط
                            <span class="text-xs font-normal text-slate-500">({{ $featuredCompaniesCount }})</span>
                        </label>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit"
                            class="inline-flex h-12 items-center justify-center rounded-xl bg-gov-800 text-sm font-bold text-white transition hover:bg-gov-700">
                            تطبيق الفلاتر
                        </button>
                        <a href="{{ route('companies.index') }}"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 text-xs font-bold text-gov-800 transition hover:border-gold-300">
                            مسح الفلاتر
                        </a>
                    </div>
                </form>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 text-center">
                    <p class="text-xs font-bold text-slate-500">إجمالي الشركات النشطة</p>
                    <p class="mt-1 text-2xl font-extrabold text-gov-950">{{ $totalActiveCompanies }}</p>
                </div>

                <div class="overflow-hidden rounded-3xl bg-gov-950 p-6 text-white">
                    <p class="text-xs font-bold text-gold-300">هل تملك شركة أو منشأة؟</p>
                    <h3 class="mt-3 text-xl font-extrabold leading-8">سجّل شركتك ضمن دليل توريدات</h3>
                    <a href="{{ route('company-registration.create') }}"
                        class="mt-6 inline-flex h-12 items-center justify-center rounded-2xl bg-gold-500 px-6 text-xs font-bold text-white transition hover:bg-gold-600">
                        سجّل شركتك
                    </a>
                </div>
            </aside>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('companiesPage', () => ({
            // Shared header/footer state
            route: 'directory',
            mobile: false,
            navStuck: false,
            nav: [
                { id: 'home', label: 'الرئيسية' },
                { id: 'directory', label: 'الشركات' },
                { id: 'categories', label: 'التصنيفات' },
                { id: 'news', label: 'الأخبار' },
                { id: 'about', label: 'من نحن' },
                { id: 'contact', label: 'تواصل معنا' }
            ],

            init() {
                this.updateStickyNav();
                window.addEventListener('scroll', () => this.updateStickyNav(), { passive: true });
                window.addEventListener('resize', () => this.updateStickyNav());
            },
            updateStickyNav() {
                const nav = document.getElementById('site-sticky-nav');
                this.navStuck = window.scrollY >= ((nav?.offsetTop || 0) - 1);
            },
            go(routeId) {
                if (routeId === 'categories') {
                    location.href = '{{ route('home') }}#companies';
                    return;
                }
                const paths = {
                    home: '{{ route('home') }}',
                    directory: '{{ route('companies.index') }}',
                    news: '{{ route('news.index') }}',
                    about: '{{ route('about') }}',
                    contact: '{{ route('contact') }}',
                    plans: '{{ route('plans') }}',
                };
                location.href = paths[routeId] || '{{ route('home') }}';
            }
        }))
    })
</script>
@endpush
