{{-- SEO title/description intentionally not overridden here: the homepage
     uses the global defaults from الإعدادات → إعدادات الموقع (see
     layouts/app.blade.php), which already fall back to sensible copy even
     before the settings seeder/admin form has ever run. --}}
@extends('layouts.app')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/images/hero-background.webp') }}" type="image/webp"
        fetchpriority="high">
@endpush

@section('content')
    <!-- Hero -->
    <section class="py-0 sm:py-0">
        <div class="w-full px-0">
            <div class="home-hero-bg relative min-h-[560px] overflow-hidden bg-gov-950 bg-cover bg-center shadow-2xl shadow-gov-900/20 lg:min-h-[600px]"
                style="--hero-bg-url: url('{{ asset('assets/images/hero-background.webp') }}')">
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_70%_20%,rgba(214,160,46,.18),transparent_28%),radial-gradient(circle_at_20%_80%,rgba(255,255,255,.08),transparent_30%)]">
                </div>

                <div class="pointer-events-none absolute bottom-8 right-8 h-72 w-72 opacity-20 text-gold-300">
                    <div class="absolute bottom-0 right-0 h-40 w-40 rotate-45 border border-current"></div>
                    <div class="absolute bottom-16 right-16 h-56 w-56 rotate-45 border border-current"></div>
                    <div class="absolute bottom-28 right-4 h-px w-80 bg-current"></div>
                    <div class="absolute bottom-4 right-24 h-64 w-px bg-current"></div>
                </div>

                <div
                    class="relative mx-auto grid max-w-[1500px] gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_430px] lg:items-center lg:px-8 lg:py-10">

                    <!-- Main Content - Right in RTL -->
                    <div
                        class="order-1 flex min-h-[500px] items-center justify-center text-center text-white lg:min-h-[520px]">
                        <div class="w-full max-w-5xl">
                            <div
                                class="inline-flex items-center gap-3 rounded-full border border-gold-300/30 bg-white/10 px-4 py-2 text-xs font-bold text-gold-200 backdrop-blur-sm">
                                <span>توريد</span>
                                <span class="h-1 w-1 rounded-full bg-gold-300"></span>
                                <span>دليل مصانع مواد البناء</span>
                            </div>

                            <h1 class="mt-7">
                                <span
                                    class="block text-5xl font-extrabold leading-tight sm:text-6xl lg:text-7xl">توريد</span>
                                <span class="mt-4 block text-3xl font-bold leading-[1.35] sm:text-4xl lg:text-[44px]">
                                    <span class="text-gold-300">دليل مصانع مواد البناء</span><br>
                                    بالمملكة العربية السعودية
                                </span>
                            </h1>

                            <p class="mx-auto mt-6 max-w-2xl text-sm leading-8 text-slate-100 sm:text-base">
                                ابحث بسهولة عن شركات البناء والمقاولين والموردين حسب المدينة أو نوع النشاط داخل المملكة.
                            </p>

                            <div class="mx-auto mt-8 max-w-[820px] rounded-[26px] bg-white p-4 shadow-2xl shadow-black/25">
                                <div class="grid gap-3 lg:grid-cols-[1fr_180px_150px]">
                                    <div
                                        class="flex h-14 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4">
                                        <label for="hero-search" class="sr-only">البحث باسم الشركة أو النشاط</label>
                                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="7" />
                                            <path d="m20 20-4-4" />
                                        </svg>
                                        <input id="hero-search" x-model="heroSearch" @keydown.enter="searchFromHero()"
                                            class="h-full w-full bg-transparent text-right text-sm text-slate-800 outline-none"
                                            placeholder="ابحث باسم الشركة أو النشاط...">
                                    </div>

                                    <label for="hero-city" class="sr-only">اختيار مدينة البحث</label>
                                    <select id="hero-city" x-model="heroCity"
                                        class="h-14 rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none">
                                        <option>جميع المدن</option>
                                        <template x-for="city in cities" :key="city">
                                            <option x-text="city"></option>
                                        </template>
                                    </select>

                                    <button @click="searchFromHero()"
                                        class="h-14 rounded-2xl bg-gov-700 px-7 text-sm font-bold text-white shadow-lg shadow-black/10 transition hover:bg-gov-800">
                                        ابحث الآن
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- News - Left in RTL -->
                    <aside
                        class="order-2 mx-auto w-full max-w-[420px] rounded-3xl border border-gov-100/80 bg-[linear-gradient(180deg,var(--color-slate-50)_0%,var(--color-gov-50)_100%)] p-5 shadow-[0_18px_38px_rgba(7,30,23,0.18)] lg:mx-0">
                        <div class="flex items-start justify-between gap-4 border-b border-gov-100 pb-4">
                            <div>
                                <h2 class="text-xl font-extrabold text-gov-950">آخر الأخبار</h2>
                                <p class="mt-2 text-xs leading-6 text-slate-600">متابعة أخبار قطاع البناء والشركات</p>
                            </div>
                            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-gold-400 ring-4 ring-gold-100"></span>
                        </div>

                        <div class="mt-4 space-y-2.5">
                            <template x-for="item in news.slice(0,3)" :key="item.slug">
                                <a :href="'{{ route('news.show', ['slug' => '__SLUG__']) }}'.replace('__SLUG__',
                                    encodeURIComponent(item.slug))"
                                    class="grid w-full grid-cols-[92px_1fr] gap-4 rounded-2xl border border-transparent bg-white/55 p-2.5 text-right transition hover:border-gov-100 hover:bg-white/85">
                                    <img :src="item.image || '{{ asset('assets/images/news-placeholder.jpg') }}'"
                                        :alt="item.title" width="92" height="86" loading="lazy" decoding="async"
                                        class="h-[86px] w-[92px] rounded-2xl object-cover">
                                    <div class="min-w-0">
                                        <span
                                            class="inline-flex rounded-full bg-gold-100 px-2.5 py-1 text-xs font-bold text-gold-700"
                                            x-text="item.category"></span>
                                        <h3 class="mt-2 line-clamp-2 text-[13px] font-bold leading-6 text-gov-950"
                                            x-text="item.title">
                                        </h3>
                                        <p class="mt-2 text-[11px] font-medium text-slate-500" x-text="item.time"></p>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <a href="{{ route('news.index') }}"
                            class="mt-5 flex h-12 w-full items-center justify-center rounded-2xl border border-gov-200 bg-white/60 text-xs font-bold text-gov-800 transition hover:border-gov-300 hover:bg-white">
                            عرض جميع الأخبار →
                        </a>
                    </aside>

                </div>
            </div>
        </div>
    </section>

    <!-- Featured Companies -->
    <section id="companies" class="bg-slate-50 pb-6 pt-6 sm:pb-8 sm:pt-8">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-gov-50 px-4 py-2 text-xs font-bold text-gov-800">
                        قطاع البناء والتوريد
                    </span>
                    <h2 class="mt-4 text-3xl font-extrabold text-gov-950 sm:text-4xl">
                        دليل الشركات السعودية
                    </h2>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-500">
                        بوابتك للوصول إلى شركات البناء والموردين المعتمدين في مختلف مناطق المملكة. </p>
                </div>

            </div>

            <div class="grid items-start gap-8 lg:grid-cols-[280px_1fr] xl:grid-cols-[300px_1fr]">

                <!-- Sidebar -->
                <aside class="lg:order-1">
                    <div class="sticky top-24 overflow-hidden rounded-3xl border border-slate-200 bg-white">
                        <div
                            class="border-b border-slate-100 bg-[linear-gradient(135deg,var(--color-white),var(--color-slate-50))] p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-extrabold text-gov-950">تصنيفات البناء</h3>
                                    <p class="mt-1 text-[11px] text-slate-500">اختر التصنيف المناسب</p>
                                </div>
                                <span class="rounded-full bg-gold-100 px-3 py-1 text-[10px] font-bold text-gold-700"
                                    x-text="categories.length"></span>
                            </div>
                        </div>

                        <div class="space-y-1.5 overflow-visible p-3">
                            <button @click="selectedCategory='جميع الشركات'"
                                class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-right transition"
                                :class="selectedCategory === 'جميع الشركات' ?
                                    'border border-gov-100 bg-gov-50 text-gov-900 shadow-sm' :
                                    'text-slate-600 hover:bg-slate-50'">
                                <span
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-white text-gov-700 shadow-sm ring-1 ring-slate-100">▦</span>
                                <span class="min-w-0 flex-1 text-xs font-bold">جميع الشركات</span>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] text-slate-500"
                                    x-text="companies.length"></span>
                            </button>

                            <template x-for="category in categories" :key="category.name">
                                <button @click="selectedCategory=category.name"
                                    class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-right transition"
                                    :class="selectedCategory === category.name ?
                                        'border border-gov-100 bg-gov-50 text-gov-900 shadow-sm' :
                                        'text-slate-600 hover:bg-slate-50'">
                                    <span
                                        class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-white text-gov-700 shadow-sm ring-1 ring-slate-100"
                                        x-text="category.icon"></span>
                                    <span class="min-w-0 flex-1 text-xs font-bold" x-text="category.name"></span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] text-slate-500"
                                        x-text="category.count"></span>
                                </button>
                            </template>

                            <p x-show="categories.length === 0" class="px-3 py-4 text-center text-xs text-slate-500">
                                لا توجد تصنيفات مضافة بعد.
                            </p>
                        </div>
                    </div>
                </aside>

                <!-- Content -->
                <div class="lg:order-2">

                    <!-- Filters -->
                    <div class="rounded-3xl border border-slate-200 bg-white p-4">
                        <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr]">
                            <div
                                class="flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 transition focus-within:border-gov-300 focus-within:bg-white">
                                <label for="company-search" class="sr-only">بحث باسم الشركة</label>
                                <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7" />
                                    <path d="m20 20-4-4" />
                                </svg>
                                <input id="company-search" x-model="companySearch"
                                    class="h-full w-full bg-transparent text-xs outline-none"
                                    placeholder="بحث باسم الشركة">
                            </div>

                            <label for="filter-city" class="sr-only">تصفية الشركات حسب المدينة</label>
                            <select id="filter-city" x-model="selectedCity"
                                class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-3 text-xs outline-none transition focus:border-gov-300 focus:bg-white">
                                <option>اختيار المدينة</option>
                                <template x-for="city in cities" :key="city">
                                    <option x-text="city"></option>
                                </template>
                            </select>

                            <label for="filter-category" class="sr-only">تصفية الشركات حسب التصنيف</label>
                            <select id="filter-category" x-model="selectedCategory"
                                class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-3 text-xs outline-none transition focus:border-gov-300 focus:bg-white">
                                <option value="جميع الشركات">اختيار التصنيف</option>
                                <template x-for="category in categories" :key="category.name">
                                    <option x-text="category.name"></option>
                                </template>
                            </select>

                            <label for="sort-companies" class="sr-only">ترتيب نتائج الشركات</label>
                            <select id="sort-companies" x-model="sortBy"
                                class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-3 text-xs outline-none transition focus:border-gov-300 focus:bg-white">
                                <option>ترتيب افتراضي</option>
                                <option>الأحدث</option>
                                <option>حسب المدينة</option>
                            </select>
                        </div>
                    </div>

                    <!-- Company results + pagination share a wrapper here so
                         overflow-anchor: none (below) can exclude this whole
                         region from browser scroll anchoring -- see .company-
                         pagination-region in resources/css/app.css. -->
                    <div class="company-pagination-region">
                        <!-- Companies Grid -->
                        <div id="companies-results"
                            class="mt-6 grid grid-cols-1 items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3">
                            <template x-for="company in paginatedCompanies" :key="company.name">
                                <article
                                    class="company-result-card group relative flex min-h-[332px] flex-col rounded-3xl border border-slate-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-gov-200 hover:shadow-[0_12px_28px_rgba(7,30,23,0.10)]">
                                    <span x-show="company.isFeatured"
                                        class="absolute end-4 top-4 inline-flex items-center gap-1 rounded-full bg-gold-50 px-2.5 py-1 text-xs font-bold text-gold-700 shadow">
                                        مميزة
                                    </span>

                                    <span x-show="company.isVerified" role="img" aria-label="شركة موثقة"
                                        title="شركة موثقة"
                                        class="absolute start-4 top-4 inline-flex h-6 w-6 items-center justify-center rounded-full bg-gov-50 text-gov-700 shadow-sm ring-1 ring-gov-100">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.4" aria-hidden="true">
                                            <path d="M9 12.5 11 14.5 15.5 9.5" />
                                        </svg>
                                    </span>

                                    <div
                                        class="mx-auto flex h-[92px] w-[92px] items-center justify-center rounded-3xl border border-slate-200 bg-white p-4 transition duration-300 group-hover:border-gov-100">
                                        <img :src="company.logo || '{{ asset('assets/images/company-placeholder.png') }}'"
                                            :alt="company.name" width="92" height="92" loading="lazy"
                                            decoding="async" class="h-full w-full object-contain object-center">
                                    </div>

                                    <h3 class="mx-auto mt-5 line-clamp-2 min-h-10 max-w-[250px] text-[18px] font-extrabold leading-7 text-gov-950"
                                        x-text="company.name"></h3>

                                    <div
                                        class="mt-3 flex items-center justify-center gap-2 text-[12px] font-semibold text-slate-500">
                                        <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z" />
                                            <circle cx="12" cy="10" r="2.5" />
                                        </svg>
                                        <span x-text="company.city"></span>
                                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                        <span x-text="company.category"></span>
                                    </div>

                                    <p class="mx-auto mt-3 line-clamp-2 min-h-12 max-w-[280px] text-[13px] leading-6 text-slate-500"
                                        x-text="company.desc"></p>

                                    <div class="mt-auto pt-6">
                                        <a x-show="company.website" :href="company.website" target="_blank"
                                            rel="noopener noreferrer"
                                            class="shine-cta flex h-12 items-center justify-center gap-2 rounded-2xl bg-gov-800 text-sm font-bold text-white shadow-[0_14px_28px_rgba(7,30,23,0.18)] transition hover:bg-gov-900 hover:shadow-[0_16px_32px_rgba(7,30,23,0.24)]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                            </svg>
                                            <span>زيارة الموقع الإلكتروني</span>
                                        </a>
                                        <span x-show="!company.website" aria-disabled="true"
                                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-200 text-sm font-bold text-slate-500">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                            </svg>
                                            <span>الموقع الإلكتروني غير متاح</span>
                                        </span>
                                    </div>
                                </article>
                            </template>

                            <!-- Invisible placeholder slots so the grid always
                                 occupies a full companiesPerPage-sized footprint,
                                 even on a shorter last page -- this keeps the
                                 pagination nav below at a constant vertical
                                 position across pages instead of jumping up/down
                                 by however many rows the real-card count differs
                                 by. Height comes from companyCardSlotHeight
                                 (home.js), which is measured live from the
                                 tallest .company-result-card actually on the
                                 page -- not a guessed fixed px value -- so a
                                 placeholder-only row can't end up shorter than
                                 a real card row once titles/descriptions wrap.
                                 Purely presentational: aria-hidden, not
                                 focusable, no pointer interaction, no content. -->
                            <template x-for="n in missingCompanySlots" :key="'company-placeholder-' + n">
                                <div class="invisible pointer-events-none" :style="`min-height: ${companyCardSlotHeight}px`" aria-hidden="true"></div>
                            </template>
                        </div>

                        <nav x-show="totalCompanyPages > 1" x-cloak
                            class="mt-11 flex flex-wrap items-center justify-center gap-2" aria-label="صفحات دليل الشركات">
                            <button type="button" @click="setCompanyPage(currentCompanyPage - 1)"
                                :disabled="currentCompanyPage === 1"
                                class="h-11 rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gov-700 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-slate-200 disabled:hover:text-slate-600">
                                السابق
                            </button>

                            <template x-for="item in companyPaginationItems" :key="item.key">
                                <span>
                                    <button x-show="item.page" type="button" @click="setCompanyPage(item.page)"
                                        :aria-label="`الانتقال إلى الصفحة ${item.page}`"
                                        :aria-current="currentCompanyPage === item.page ? 'page' : null"
                                        class="grid h-11 w-11 place-items-center rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gov-700 focus-visible:ring-offset-2"
                                        :class="currentCompanyPage === item.page ?
                                            'border-gov-800 bg-gov-800 font-bold text-white' :
                                            'border-slate-200 font-semibold text-slate-600 hover:border-gov-300 hover:text-gov-900'"
                                        x-text="item.page"></button>
                                    <span x-show="!item.page" class="grid h-11 w-8 place-items-center text-slate-400"
                                        aria-hidden="true">…</span>
                                </span>
                            </template>

                            <button type="button" @click="setCompanyPage(currentCompanyPage + 1)"
                                :disabled="currentCompanyPage === totalCompanyPages"
                                class="h-11 rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gov-700 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-slate-200 disabled:hover:text-slate-600">
                                التالي
                            </button>
                        </nav>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredCompanies.length===0"
                        class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center">
                        <p class="font-bold text-gov-900">لا توجد شركات مطابقة</p>
                        <p class="mx-auto mt-2 max-w-md text-xs leading-6 text-slate-500">
                            جرّب تغيير التصنيف أو المدينة أو كلمات البحث.
                        </p>
                        <button @click="resetFilters()"
                            class="mt-5 rounded-2xl bg-gov-800 px-5 py-3 text-xs font-bold text-white">
                            إعادة ضبط الفلاتر
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Advertising Spaces -->
    <section class="bg-slate-50 pb-14 pt-6 sm:pb-20 sm:pt-8">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="mb-9 text-center">
                <p class="text-xs font-bold text-gov-600">فرص إعلانية</p>
                <h2 class="mt-3 text-4xl font-extrabold text-gov-950">مساحات إعلانية</h2>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-500">
                    عزّز من ظهور شركتك أمام آلاف المقاولين والموردين يومياً.
                </p>
            </div>

            <!-- Main Ad -->
            @if ($homeBanner1?->image)
                <a href="{{ route('ads.click', $homeBanner1) }}"
                    class="relative block overflow-hidden rounded-[36px] bg-gov-950 shadow-2xl shadow-gov-900/20 ring-1 ring-gov-900/10">
                    <span
                        class="absolute start-4 top-4 z-10 rounded-full bg-black/40 px-3 py-1 text-xs font-bold text-white backdrop-blur-sm">إعلان</span>
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner1->image) }}"
                        alt="{{ $homeBanner1->title }}"
                        width="{{ $homeAdvertisementSlots->get('home_banner_1')?->width ?: 1600 }}"
                        height="{{ $homeAdvertisementSlots->get('home_banner_1')?->height ?: 400 }}" loading="lazy"
                        decoding="async" class="block h-auto w-full object-contain">
                </a>
            @else
                <div
                    class="relative overflow-hidden rounded-[36px] bg-gov-950 shadow-2xl shadow-gov-900/20 ring-1 ring-gov-900/10">
                    <img src="{{ asset('assets/images/ad-section-bg.jpg') }}" alt="" width="1600"
                        height="900" loading="lazy" decoding="async"
                        class="absolute inset-0 h-full w-full object-cover opacity-65">
                    <div
                        class="absolute inset-0 bg-[linear-gradient(90deg,rgba(var(--gov-950-rgb),.88),rgba(var(--gov-925-rgb),.76),rgba(var(--gov-950-rgb),.88))]">
                    </div>
                    <div
                        class="pointer-events-none absolute -left-12 top-10 h-32 w-32 rotate-12 rounded-[32px] border border-gold-300 opacity-20">
                    </div>
                    <div
                        class="pointer-events-none absolute bottom-10 right-12 h-20 w-20 rotate-45 border border-gold-300 opacity-20">
                    </div>

                    <div class="relative flex min-h-[280px] items-center p-6 text-center sm:p-8 lg:p-12 lg:text-right">
                        <div class="mx-auto max-w-3xl lg:mx-0">
                            <span
                                class="inline-flex rounded-full border border-gold-300/30 bg-white/10 px-4 py-2 text-xs font-bold text-gold-200 backdrop-blur-sm">
                                مساحة إعلانية رئيسية
                            </span>
                            <h3 class="mt-5 text-3xl font-extrabold leading-[1.35] text-white sm:text-4xl lg:text-[42px]">
                                اعرض شركتك في المكان الأبرز على منصة توريد
                            </h3>
                            <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-100 sm:text-base lg:mx-0">
                                ظهور مباشر أمام الباحثين عن شركات البناء والموردين داخل المملكة.
                            </p>
                            <a href="{{ $homeBanner1?->link ? route('ads.click', $homeBanner1) : route('contact') }}"
                                class="shine-cta mt-8 inline-flex h-14 items-center justify-center rounded-2xl bg-gov-700 px-10 text-base font-bold text-white shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-gov-800">
                                احجز إعلانك الآن
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ad Cards -->
            <div class="mt-8 grid gap-5 lg:grid-cols-3">
                @if ($homeBanner2?->image)
                    <a href="{{ route('ads.click', $homeBanner2) }}"
                        class="reveal-up group relative block self-start overflow-hidden rounded-3xl bg-gov-950">
                        <span
                            class="absolute start-3 top-3 z-10 rounded-full bg-black/40 px-2 py-1 text-xs font-bold text-white backdrop-blur-sm">إعلان</span>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner2->image) }}"
                            alt="{{ $homeBanner2->title }}"
                            width="{{ $homeAdvertisementSlots->get('home_banner_2')?->width ?: 320 }}"
                            height="{{ $homeAdvertisementSlots->get('home_banner_2')?->height ?: 213 }}" loading="lazy"
                            decoding="async" class="block h-auto w-full object-contain">
                    </a>
                @else
                    <article
                        class="reveal-up group relative flex min-h-[300px] flex-col justify-end overflow-hidden rounded-3xl bg-gov-950 p-7 text-white">
                        <img src="{{ asset('assets/images/ad-section-bg.jpg') }}" alt="" width="1600"
                            height="900" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-300 group-hover:scale-105">
                        <div
                            class="absolute inset-0 bg-[linear-gradient(180deg,rgba(var(--gov-950-rgb),.25),rgba(var(--gov-950-rgb),.9))]">
                        </div>
                        <div class="relative">
                            <span
                                class="rounded-full border border-gold-300/30 bg-white/10 px-3 py-1 text-[11px] font-bold text-gold-200">مساحة
                                رئيسية</span>
                            <h3 class="mt-5 text-2xl font-extrabold leading-9">ظهور بارز في الصفحة الرئيسية</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-100">بنر واسع مناسب لإطلاق العروض وتعزيز حضور
                                العلامة.</p>
                            <a href="{{ $headerBanner?->link ? route('ads.click', $headerBanner) : route('contact') }}"
                                class="shine-cta mt-7 inline-flex h-11 items-center rounded-xl bg-gov-700 px-5 text-xs font-bold text-white transition hover:bg-gov-800">احجز
                                الآن</a>
                        </div>
                    </article>
                @endif

                @if ($homeBanner3?->image)
                    <a href="{{ route('ads.click', $homeBanner3) }}"
                        class="reveal-up group relative block self-start overflow-hidden rounded-3xl bg-gov-950 [animation-delay:80ms]">
                        <span
                            class="absolute start-3 top-3 z-10 rounded-full bg-black/40 px-2 py-1 text-xs font-bold text-white backdrop-blur-sm">إعلان</span>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner3->image) }}"
                            alt="{{ $homeBanner3->title }}"
                            width="{{ $homeAdvertisementSlots->get('home_banner_3')?->width ?: 320 }}"
                            height="{{ $homeAdvertisementSlots->get('home_banner_3')?->height ?: 213 }}" loading="lazy"
                            decoding="async" class="block h-auto w-full object-contain">
                    </a>
                @else
                    <article
                        class="reveal-up group relative flex min-h-[300px] flex-col justify-end overflow-hidden rounded-3xl bg-gov-950 p-7 text-white [animation-delay:80ms]">
                        <img src="{{ asset('assets/images/ad-card-1.jpg') }}" alt="" width="320"
                            height="213" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-300 group-hover:scale-105">
                        <div
                            class="absolute inset-0 bg-[linear-gradient(180deg,rgba(var(--gov-950-rgb),.22),rgba(var(--gov-950-rgb),.9))]">
                        </div>
                        <div class="relative">
                            <span
                                class="rounded-full border border-gold-300/30 bg-white/10 px-3 py-1 text-[11px] font-bold text-gold-200">مساحة
                                متوسطة</span>
                            <h3 class="mt-5 text-2xl font-extrabold leading-9">وصول مستمر لجمهور مستهدف</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-100">مناسبة لشركات مواد البناء والمقاولين
                                والتشطيبات.</p>
                            <a href="{{ $homeBanner3?->link ? route('ads.click', $homeBanner3) : route('contact') }}"
                                class="shine-cta mt-7 inline-flex h-11 items-center rounded-xl bg-gov-700 px-5 text-xs font-bold text-white transition hover:bg-gov-800">احجز
                                الآن</a>
                        </div>
                    </article>
                @endif

                @if ($homeBanner4?->image)
                    <a href="{{ route('ads.click', $homeBanner4) }}"
                        class="reveal-up group relative block self-start overflow-hidden rounded-3xl bg-gov-950 [animation-delay:160ms]">
                        <span
                            class="absolute start-3 top-3 z-10 rounded-full bg-black/40 px-2 py-1 text-xs font-bold text-white backdrop-blur-sm">إعلان</span>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner4->image) }}"
                            alt="{{ $homeBanner4->title }}"
                            width="{{ $homeAdvertisementSlots->get('home_banner_4')?->width ?: 320 }}"
                            height="{{ $homeAdvertisementSlots->get('home_banner_4')?->height ?: 480 }}" loading="lazy"
                            decoding="async" class="block h-auto w-full object-contain">
                    </a>
                @else
                    <article
                        class="reveal-up group relative flex min-h-[300px] flex-col justify-end overflow-hidden rounded-3xl bg-gov-950 p-7 text-white [animation-delay:160ms]">
                        <img src="{{ asset('assets/images/ad-card-2.jpg') }}" alt="" width="320"
                            height="480" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-300 group-hover:scale-105">
                        <div
                            class="absolute inset-0 bg-[linear-gradient(180deg,rgba(var(--gov-950-rgb),.22),rgba(var(--gov-950-rgb),.9))]">
                        </div>
                        <div class="relative">
                            <span
                                class="rounded-full border border-gold-300/30 bg-white/10 px-3 py-1 text-[11px] font-bold text-gold-200">مساحة
                                جانبية</span>
                            <h3 class="mt-5 text-2xl font-extrabold leading-9">حضور ثابت داخل صفحات الدليل</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-100">خيار عملي للظهور بجانب التصنيفات وصفحات
                                الشركات.</p>
                            <a href="{{ $homeBanner4?->link ? route('ads.click', $homeBanner4) : route('contact') }}"
                                class="shine-cta mt-7 inline-flex h-11 items-center rounded-xl bg-gov-700 px-5 text-xs font-bold text-white transition hover:bg-gov-800">احجز
                                الآن</a>
                        </div>
                    </article>
                @endif
            </div>

            <!-- Bottom CTA -->
            <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-7 lg:flex lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-2xl font-extrabold text-gov-950">ابدأ الآن في تطوير حضور شركتك</h3>
                    <p class="mt-2 text-sm text-slate-500">اختر المساحة الإعلانية المناسبة وتواصل معنا للحجز.</p>
                </div>
                <a href="{{ route('contact') }}"
                    class="shine-cta mt-5 inline-flex h-12 items-center justify-center rounded-2xl bg-gov-800 px-8 text-sm font-bold text-white transition hover:bg-gov-900 lg:mt-0">
                    تواصل معنا
                </a>
            </div>

        </div>
    </section>

    <!-- Partner Logos -->
    @if (count($dbPartnerLogos) > 0)
        <section class="bg-slate-50 py-6 sm:py-16">
            <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

                <div class="mx-auto max-w-3xl text-center">
                    <span class="inline-flex rounded-full bg-gov-50 px-3 py-1 text-[11px] font-bold text-gov-800">
                        نحن طريقك الى
                    </span>

                    <h2 class="mt-4 text-3xl font-extrabold leading-10 text-gov-950">
                        يثقون بنا
                    </h2>

                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        نخبة من الشركات والموردين المتعاملين ضمن منصة توريد.
                    </p>
                </div>

                {{-- Skin + motion reference: logocarousel.com/carousel "Custom
                 Margin & GrayScale on hover Normal" (measured via DevTools —
                 see the CSS comment above): thin #d8d8d8 1px border, 5px
                 radius, 25px inner padding, 20px gap, 600ms transform
                 transition, single-item auto-advance every 6s, dot pagination,
                 drag/swipe. Logos are grayscale by default and reveal color on
                 hover/focus — the opposite of the reference default, per an
                 explicit product decision (see CSS comment for the other
                 intentional difference, object-fit: contain). --}}
                <div class="logos-carousel relative mt-8" role="region" aria-roledescription="carousel"
                    aria-label="شعارات الشركاء" x-data="partnerLogosCarousel(partnerLogos)" @focusin="clearInterval(logosTimer)"
                    @focusout="restartLogosAutoplay()">

                    {{-- Screen-reader list: the visual track duplicates logos for the
                     infinite-loop illusion and is hidden from assistive tech
                     (aria-hidden below) — this is the one real, non-duplicated
                     announcement of the partner list. --}}
                    <ul class="sr-only">
                        <template x-for="logo in partnerLogos" :key="logo.name">
                            <li>
                                <template x-if="logo.link">
                                    <a :href="logo.link" target="_blank" rel="noopener noreferrer"
                                        x-text="logo.name"></a>
                                </template>
                                <template x-if="!logo.link">
                                    <span x-text="logo.name"></span>
                                </template>
                            </li>
                        </template>
                    </ul>

                    <div class="logos-carousel__viewport" x-ref="logosViewport" aria-hidden="true"
                        :class="{ 'is-dragging': logosDragging }" @pointerdown="logosDragStart($event)"
                        @pointermove="logosDragMove($event)" @pointerup.window="logosDragEnd()"
                        @pointercancel="logosDragEnd()" @lostpointercapture="logosDragEnd()" @dragstart.prevent
                        @click.capture="if (logosDidDrag) { $event.preventDefault(); $event.stopPropagation(); }">
                        <div class="logos-carousel__track" :class="{ 'is-instant': logosInstant }"
                            :style="logosTrackStyle">
                            <template x-for="item in logosExtended" :key="item.key">
                                <div class="logos-carousel__card">
                                    <template x-if="item.link">
                                        <a :href="item.link" target="_blank" rel="noopener noreferrer"
                                            tabindex="-1">
                                            <img :src="item.logo" :alt="item.name" width="140"
                                                height="57" loading="lazy" decoding="async" draggable="false">
                                        </a>
                                    </template>
                                    <template x-if="!item.link">
                                        <img :src="item.logo" :alt="item.name" width="140" height="57"
                                            loading="lazy" decoding="async" draggable="false">
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-2">
                        <template x-for="(logo, i) in partnerLogos" :key="i">
                            <button type="button" class="logos-carousel__dot"
                                :class="{ 'is-active': logosActiveDot === i }" @click="goToLogo(i)"
                                :aria-label="`الانتقال إلى ${logo.name}`"
                                :aria-current="logosActiveDot === i ? 'true' : 'false'"></button>
                        </template>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <div x-show="toast" x-cloak x-transition role="status" aria-live="polite"
        class="fixed bottom-6 right-1/2 z-[70] w-[calc(100%-2rem)] max-w-md translate-x-1/2 rounded-2xl border border-gold-300 bg-gov-950 px-5 py-4 text-center text-xs font-semibold text-white shadow-2xl"
        x-text="toast"></div>
@endsection

@push('scripts')
    <script>
        // Minimal server-to-JS config bridge. All Alpine component logic
        // (state, methods, getters) lives in resources/js/pages/home.js,
        // registered via resources/js/app.js — this object is the ONLY
        // thing Blade hands over: server-generated data and route URLs,
        // nothing else. See home.js for how each field is consumed.
        //
        // Single source of truth for the "Partner Logos" carousel below —
        // admin-managed via Filament (المحتوى → شعارات الشركاء) and
        // queried in HomeController, not hardcoded in JS.
        window.tawreedatHomeConfig = {
            partnerLogos: @json($dbPartnerLogos),
            cities: @json($dbCities),
            categories: @json($dbCategories),
            companies: @json($dbCompanies),
            news: @json($dbNews),
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
                },
            ],
            routePathMap: {
                '{{ parse_url(route('home'), PHP_URL_PATH) ?: '/' }}': 'home',
                '{{ parse_url(route('companies.index'), PHP_URL_PATH) }}': 'directory',
                '{{ parse_url(route('news.index'), PHP_URL_PATH) }}': 'news',
                '{{ parse_url(route('about'), PHP_URL_PATH) }}': 'about',
                '{{ parse_url(route('contact'), PHP_URL_PATH) }}': 'contact',
                '{{ parse_url(route('plans'), PHP_URL_PATH) }}': 'plans',
            },
            routes: {
                companiesIndex: '{{ route('companies.index') }}',
                newsShowTemplate: '{{ route('news.show', ['slug' => '__SLUG__']) }}',
            },
        };
    </script>
@endpush
