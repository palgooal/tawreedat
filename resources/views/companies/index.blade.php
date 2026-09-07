@php($seoTitle = 'دليل الشركات والموردين | توريد')
@php($seoDescription = 'استعرض الشركات والموردين المسجلين في منصة توريد حسب المدينة والتصنيف وحالة التوثيق.')
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
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,50,42,.95),rgba(0,58,48,.88),rgba(0,50,42,.95))]">
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
                    استعرض الشركات والموردين المسجلين في توريد حسب المدينة أو التصنيف أو حالة التوثيق.
                </p>
            </div>
        </div>
    </section>

    <!-- Directory -->
    <section class="pb-16 pt-6 [contain-intrinsic-size:900px] [content-visibility:auto]">
        <div id="companies-directory" data-companies-index-url="{{ route('companies.index') }}"
            class="mx-auto grid max-w-[1500px] gap-8 px-4 sm:px-6 lg:grid-cols-[300px_1fr] lg:px-8">

            <!-- Content (right/main in RTL) -->
            <div class="lg:col-start-2 lg:row-start-1">
                <div id="companies-directory-results">
                    @include('companies.partials.results')
                </div>
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
                    <h3 class="mt-3 text-xl font-extrabold leading-8">سجّل شركتك ضمن دليل توريد</h3>
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
                nav: [{
                        id: 'home',
                        label: 'الرئيسية',
                        href: '{{ route('home') }}'
                    },
                    {
                        id: 'directory',
                        label: 'الشركات',
                        href: '{{ route('companies.index') }}'
                    },
                    {
                        id: 'categories',
                        label: 'التصنيفات',
                        href: '{{ route('home') }}#companies'
                    },
                    {
                        id: 'news',
                        label: 'الأخبار',
                        href: '{{ route('news.index') }}'
                    },
                    {
                        id: 'about',
                        label: 'من نحن',
                        href: '{{ route('about') }}'
                    },
                    {
                        id: 'contact',
                        label: 'تواصل معنا',
                        href: '{{ route('contact') }}'
                    }
                ],

                init() {
                    this.updateStickyNav();
                    window.addEventListener('scroll', () => this.updateStickyNav(), {
                        passive: true
                    });
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
