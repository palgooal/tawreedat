{{-- SEO title/description intentionally not overridden here: the homepage
     uses the global defaults from الإعدادات → إعدادات الموقع (see
     layouts/app.blade.php), which already fall back to sensible copy even
     before the settings seeder/admin form has ever run. --}}
@extends('layouts.app')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/images/hero-background.webp') }}" type="image/webp" fetchpriority="high">
    <style>
        .entities-slider__viewport {
            overflow: hidden;
            /* Headroom for the hover tooltip below — padding is never clipped by
               this element's own overflow, only content extending past the box
               is, so the tooltip has room to render without needing to escape
               the box (escaping it, via overflow-x-hidden, reliably froze the
               page's renderer here during an earlier version of this section). */
            padding-top: 48px;
        }

        .entities-slider__track {
            display: flex;
            transition: transform 500ms ease;
        }

        .entities-slider__page {
            flex: 0 0 100%;
            display: grid;
            gap: 1.25rem;
        }

        .entities-slider__dot {
            width: 0.5rem;
            height: 0.5rem;
            padding: 0;
            border: 0;
            border-radius: 9999px;
            background-color: #cbd5e1;
            cursor: pointer;
            transition: width 300ms ease, background-color 300ms ease;
        }

        .entities-slider__dot.is-active {
            width: 1.5rem;
            background-color: var(--color-gov-800, #144f34);
        }

        @media (prefers-reduced-motion: reduce) {
            .entities-slider__track {
                transition: none;
            }
        }

        /* Card hover tooltip — plain CSS (not Tailwind utility classes). The
           compiled app.css on this box has lagged behind template edits before
           (new arbitrary-value utilities silently had no rule yet), so this is
           guaranteed to render without depending on a fresh `npm run build`. */
        .entities-slider__tooltip,
        .entities-slider__tooltip-arrow {
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            margin-inline: auto;
            opacity: 0;
            pointer-events: none;
            transition: opacity 200ms ease;
        }

        .entities-slider__tooltip {
            margin-bottom: 0.5rem;
            width: max-content;
            /* Fixed cap, not a % of the card: at 2-per-page on mobile a card is
               only ~150px wide, and a long entity name ("هيئة المحتوى المحلي
               والمشتريات الحكومية") is ~300px+ at this font size — with
               white-space:nowrap and a %-based cap that text overflowed the
               tooltip's background visibly. Overflow+ellipsis is a safety net
               for names longer than even this fixed cap. */
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border-radius: 0.375rem;
            background-color: var(--color-gov-950, #012c26);
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, .3);
        }

        .entities-slider__tooltip-arrow {
            margin-bottom: 2px;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid var(--color-gov-950, #012c26);
        }

        .entities-slider__card:hover .entities-slider__tooltip,
        .entities-slider__card:hover .entities-slider__tooltip-arrow {
            opacity: 1;
        }

        /* Partner Logos carousel — modeled on logocarousel.com/carousel's
           "Custom Margin & GrayScale on hover Normal" demo (measured via
           DevTools: 0.8px #d8d8d8 card border, 5px radius, 25px inner padding,
           20px gap, 600ms slide transition). Two intentional differences from
           that reference, per explicit product spec: (1) logos are grayscale
           by default and return to color on hover — the reference demo is the
           opposite (color by default, grayscale on hover); (2) images use
           object-fit: contain, not the reference's fill, to avoid distortion. */
        .logos-carousel__viewport {
            overflow: hidden;
            touch-action: pan-y;
            cursor: grab;
            /* Forced LTR here only: keeps the slide direction identical to the
               reference (new logos enter from the right, exit left) without
               touching the page's own RTL layout — flex order and transform
               math both stay simple and match the reference 1:1. */
            direction: ltr;
        }

        .logos-carousel__viewport.is-dragging {
            cursor: grabbing;
        }

        .logos-carousel__track {
            display: flex;
            transition: transform 600ms ease;
            will-change: transform;
        }

        .logos-carousel__track.is-instant {
            transition: none;
        }

        .logos-carousel__card {
            /* Width is set inline per-card from JS (measureLogosStep), not a
               CSS percentage: the track (this card's flex container) has no
               definite width of its own — it's exactly as wide as its cards
               sum to, since it must overflow the viewport to hold the loop's
               clones — so a percentage flex-basis here would resolve against
               an indefinite size. The viewport's own (definite) width divided
               by logosPerView is the only reliable source for this number. */
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            height: 107px;
            margin-right: 20px;
            padding: 25px;
            border: 1px solid #d8d8d8;
            border-radius: 5px;
            background-color: #fff;
        }

        .logos-carousel__card img {
            /* width/height:100% (not max-*) so the logo actually grows to
               fill the padded box up to its edges — the img tag still
               carries width/height attributes for CLS/aspect-ratio
               purposes, but those are just an initial-load hint here, not
               the rendered size; object-fit:contain does the real scaling
               without distortion regardless of the source file's aspect
               ratio. Previously this was max-width/max-height:100%, which
               only caps an oversized image — it does nothing for a source
               image smaller than the box, so real uploads (and the seeded
               placeholder logos) rendered at a fixed 140x57px no matter how
               much larger the card actually was. */
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: grayscale(100%);
            transition: filter 300ms ease;
            -webkit-user-drag: none;
            user-select: none;
        }

        .logos-carousel__card:hover img,
        .logos-carousel__card:focus-visible img {
            filter: grayscale(0%);
        }

        .logos-carousel__dot {
            width: 20px;
            height: 5px;
            padding: 0;
            border: 0;
            border-radius: 2px;
            background-color: #b5b5b5;
            opacity: .35;
            cursor: pointer;
            transition: opacity 300ms ease, background-color 300ms ease;
        }

        .logos-carousel__dot.is-active {
            background-color: #16a08b;
            opacity: 1;
        }

        @media (prefers-reduced-motion: reduce) {
            .logos-carousel__track {
                transition: none;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Hero -->
    <section class="py-0 sm:py-0">
        <div class="w-full px-0">
            <div
                class="relative min-h-[560px] overflow-hidden bg-gov-950 bg-cover bg-center shadow-2xl shadow-gov-900/20 lg:min-h-[600px]"
                style="background-image: linear-gradient(90deg, rgba(0,50,42,.96) 0%, rgba(0,58,48,.88) 45%, rgba(0,50,42,.94) 100%), url('{{ asset('assets/images/hero-background.webp') }}');">
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
                    <section
                        class="order-1 flex min-h-[500px] items-center justify-center text-center text-white lg:min-h-[520px]">
                        <div class="w-full max-w-5xl">
                            <div
                                class="inline-flex items-center gap-3 rounded-full border border-gold-300/30 bg-white/10 px-4 py-2 text-xs font-bold text-gold-200 backdrop-blur-sm">
                                <span>توريد</span>
                                <span class="h-1 w-1 rounded-full bg-gold-300"></span>
                                <span>دليل مصانع مواد البناء</span>
                            </div>

                            <h1 class="mt-7">
                                <span class="block text-5xl font-extrabold leading-tight sm:text-6xl lg:text-7xl">توريد</span>
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
                                    <div class="flex h-14 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4">
                                        <label for="hero-search" class="sr-only">البحث باسم الشركة أو النشاط</label>
                                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
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
                                        class="h-14 rounded-2xl bg-gold-500 px-7 text-sm font-bold text-white shadow-lg shadow-black/10 transition hover:bg-gold-600">
                                        ابحث الآن
                                    </button>
                                </div>
                            </div>

                            <!--<button @click="go('directory')"
                                class="mx-auto mt-5 inline-flex h-12 items-center justify-center rounded-2xl border border-gold-400 px-8 text-sm font-bold text-gold-300 transition hover:bg-gold-500 hover:text-white">
                                تصفح الشركات
                            </button>-->

                        </div>
                    </section>

                    <!-- News - Left in RTL -->
                    <aside
                        class="order-2 mx-auto w-full max-w-[420px] rounded-3xl border border-gov-100/80 bg-[linear-gradient(180deg,#f8fbf9_0%,#eef5f1_100%)] p-5 shadow-[0_18px_38px_rgba(7,30,23,0.18)] lg:mx-0">
                        <div class="flex items-start justify-between gap-4 border-b border-gov-100 pb-4">
                            <div>
                                <h2 class="text-xl font-extrabold text-gov-950">آخر الأخبار</h2>
                                <p class="mt-2 text-xs leading-6 text-slate-600">متابعة أخبار قطاع البناء والشركات</p>
                            </div>
                            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-gold-400 ring-4 ring-gold-100"></span>
                        </div>

                        <div class="mt-4 space-y-2.5">
                            <template x-for="item in news.slice(0,3)" :key="item.slug">
                                <button @click="openNews(item)"
                                    class="grid w-full grid-cols-[92px_1fr] gap-4 rounded-2xl border border-transparent bg-white/55 p-2.5 text-right transition hover:border-gov-100 hover:bg-white/85">
                                    <img :src="item.image || '{{ asset('assets/images/news-placeholder.jpg') }}'" :alt="item.title" width="92" height="86"
                                        decoding="async"
                                        class="h-[86px] w-[92px] rounded-2xl object-cover">
                                    <div class="min-w-0">
                                        <span class="inline-flex rounded-full bg-gold-100 px-2.5 py-1 text-[10px] font-bold text-gold-800"
                                            x-text="item.category"></span>
                                        <h3 class="mt-2 line-clamp-2 text-[13px] font-bold leading-6 text-gov-950" x-text="item.title">
                                        </h3>
                                        <p class="mt-2 text-[11px] font-medium text-slate-500" x-text="item.time"></p>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <button @click="go('news')"
                            class="mt-5 h-12 w-full rounded-2xl border border-gov-200 bg-white/60 text-xs font-bold text-gov-800 transition hover:border-gov-300 hover:bg-white">
                            عرض جميع الأخبار →
                        </button>
                    </aside>

                </div>
            </div>
        </div>
    </section>

    <!-- Featured Companies -->
    <section id="companies" class="bg-[#fbfcfb] pb-6 pt-6 sm:pb-8 sm:pt-8">
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

                <!-- <button @click="go('directory')"
                    class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 text-xs font-bold text-gov-800 transition hover:border-gov-200 hover:bg-gov-800 hover:text-white">
                    عرض جميع الشركات ←
                </button> -->
            </div>

            <div class="grid items-start gap-8 lg:grid-cols-[280px_1fr] xl:grid-cols-[300px_1fr]">

                <!-- Sidebar -->
                <aside class="lg:order-1">
                    <div class="sticky top-24 overflow-hidden rounded-3xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 bg-[linear-gradient(135deg,#ffffff,#f8fafc)] p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-extrabold text-gov-950">تصنيفات البناء</h3>
                                    <p class="mt-1 text-[11px] text-slate-500">اختر التصنيف المناسب</p>
                                </div>
                                <span class="rounded-full bg-gold-100 px-3 py-1 text-[10px] font-bold text-gold-700" x-text="categories.length"></span>
                            </div>
                        </div>

                        <div class="space-y-1.5 overflow-visible p-3">
                            <button @click="selectedCategory='جميع الشركات'"
                                class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-right transition" :class="selectedCategory==='جميع الشركات'
                            ? 'border border-gov-100 bg-gov-50 text-gov-900 shadow-sm'
                            : 'text-slate-600 hover:bg-slate-50'">
                                <span
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-white text-gov-700 shadow-sm ring-1 ring-slate-100">▦</span>
                                <span class="min-w-0 flex-1 text-xs font-bold">جميع الشركات</span>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] text-slate-500"
                                    x-text="companies.length"></span>
                            </button>

                            <template x-for="category in categories" :key="category.name">
                                <button @click="selectedCategory=category.name"
                                    class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-right transition" :class="selectedCategory===category.name
                              ? 'border border-gov-100 bg-gov-50 text-gov-900 shadow-sm'
                              : 'text-slate-600 hover:bg-slate-50'">
                                    <span
                                        class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-white text-gov-700 shadow-sm ring-1 ring-slate-100"
                                        x-text="category.icon"></span>
                                    <span class="min-w-0 flex-1 text-xs font-bold" x-text="category.name"></span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] text-slate-500"
                                        x-text="category.count"></span>
                                </button>
                            </template>

                            <p x-show="categories.length === 0" class="px-3 py-4 text-center text-[11px] text-slate-400">
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
                                <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="11" cy="11" r="7" />
                                    <path d="m20 20-4-4" />
                                </svg>
                                <input id="company-search" x-model="companySearch" class="h-full w-full bg-transparent text-xs outline-none"
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

                    <!-- Companies Grid -->
                    <div class="mt-6 grid grid-cols-1 items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <template x-for="(company, index) in filteredCompanies.slice(0,9)" :key="company.name">
                            <article
                                class="reveal-up group relative flex min-h-[332px] flex-col rounded-3xl border border-slate-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-gov-200 hover:shadow-[0_12px_28px_rgba(7,30,23,0.10)]"
                                :style="`animation-delay:${index * 70}ms`">
                                <span x-show="company.isFeatured"
                                    class="absolute end-4 top-4 inline-flex items-center gap-1 rounded-full bg-gold-500 px-2.5 py-1 text-[10px] font-bold text-white shadow">
                                    مميزة
                                </span>

                                <span x-show="company.isVerified" title="شركة موثقة"
                                    class="absolute start-4 top-4 inline-flex h-6 w-6 items-center justify-center rounded-full bg-gov-50 text-gov-700 shadow-sm ring-1 ring-gov-100">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <path d="M9 12.5 11 14.5 15.5 9.5" />
                                    </svg>
                                </span>

                                <div
                                    class="mx-auto flex h-[92px] w-[92px] items-center justify-center rounded-3xl border border-slate-200 bg-white p-4 transition duration-300 group-hover:border-gov-100">
                                    <img :src="company.logo || '{{ asset('assets/images/company-placeholder.png') }}'" :alt="company.name"
                                        width="92" height="92" loading="lazy" decoding="async"
                                        class="h-full w-full object-contain object-center">
                                </div>

                                <h3
                                    class="mx-auto mt-5 line-clamp-2 min-h-10 max-w-[250px] text-[18px] font-extrabold leading-7 text-gov-950"
                                    x-text="company.name"></h3>

                                <div class="mt-3 flex items-center justify-center gap-2 text-[12px] font-semibold text-slate-500">
                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
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
                                    <a x-show="company.website" :href="company.website" target="_blank" rel="noopener noreferrer"
                                        class="shine-cta flex h-12 items-center justify-center gap-2 rounded-2xl bg-gov-800 text-sm font-bold text-white shadow-[0_14px_28px_rgba(7,30,23,0.18)] transition hover:bg-gov-900 hover:shadow-[0_16px_32px_rgba(7,30,23,0.24)]">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                        </svg>
                                        <span>زيارة الموقع الإلكتروني</span>
                                    </a>
                                    <span x-show="!company.website" aria-disabled="true"
                                        class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-200 text-sm font-bold text-slate-500">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                        </svg>
                                        <span>الموقع الإلكتروني غير متاح</span>
                                    </span>
                                </div>
                            </article>
                        </template>
                    </div>

                    <div x-show="filteredCompanies.length>0" class="mt-11 flex flex-wrap items-center justify-center gap-2">
                        <button
                            class="h-11 rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">
                            السابق
                        </button>
                        <button class="grid h-11 w-11 place-items-center rounded-2xl bg-gov-800 text-sm font-bold text-white">
                            1
                        </button>
                        <button
                            class="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-sm font-semibold text-slate-600 transition hover:border-gov-300">
                            2
                        </button>
                        <button
                            class="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-sm font-semibold text-slate-600 transition hover:border-gov-300">
                            3
                        </button>
                        <button
                            class="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-sm font-semibold text-slate-600 transition hover:border-gov-300">
                            4
                        </button>
                        <button
                            class="h-11 rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">
                            التالي
                        </button>
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
    <section class="bg-[#fbfcfb] pb-14 pt-6 sm:pb-20 sm:pt-8">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="mb-9 text-center">
                <p class="text-xs font-bold text-gold-600">مساحات إعلانية</p>
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
                        style="top: 16px; right: 16px; background-color: rgba(0,0,0,.4)"
                        class="absolute z-10 rounded-full px-3 py-1 text-[11px] font-bold text-white backdrop-blur-sm">إعلان</span>
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner1->image) }}"
                        alt="{{ $homeBanner1->title }}" width="1600" height="400" loading="lazy" decoding="async"
                        style="max-height: 400px" class="h-auto w-full object-cover">
                </a>
            @else
                <div
                    class="relative overflow-hidden rounded-[36px] bg-gov-950 shadow-2xl shadow-gov-900/20 ring-1 ring-gov-900/10">
                    <img src="{{ asset('assets/images/ad-section-bg.jpg') }}"
                        alt="" width="1600" height="900" loading="lazy" decoding="async"
                        class="absolute inset-0 h-full w-full object-cover opacity-65">
                    <div
                        class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,50,42,.88),rgba(0,58,48,.76),rgba(0,50,42,.88))]">
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
                                class="shine-cta mt-8 inline-flex h-14 items-center justify-center rounded-2xl bg-gold-500 px-10 text-base font-bold text-white shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-gold-600">
                                احجز إعلانك الآن
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ad Cards -->
            <div class="mt-8 grid gap-5 lg:grid-cols-3">
                @if ($headerBanner?->image)
                    <a href="{{ route('ads.click', $headerBanner) }}"
                        class="reveal-up group relative flex min-h-[300px] overflow-hidden rounded-3xl bg-gov-950">
                        <span
                            style="top: 12px; right: 12px; background-color: rgba(0,0,0,.4)"
                            class="absolute z-10 rounded-full px-2 py-1 text-[10px] font-bold text-white backdrop-blur-sm">إعلان</span>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($headerBanner->image) }}"
                            alt="{{ $headerBanner->title }}" width="1600" height="900" loading="lazy" decoding="async"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    </a>
                @else
                    <article
                        class="reveal-up group relative flex min-h-[300px] flex-col justify-end overflow-hidden rounded-3xl bg-gov-950 p-7 text-white">
                        <img src="{{ asset('assets/images/ad-section-bg.jpg') }}"
                            alt="" width="1600" height="900" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(0,50,42,.25),rgba(0,50,42,.9))]"></div>
                        <div class="relative">
                            <span
                                class="rounded-full border border-gold-300/30 bg-white/10 px-3 py-1 text-[11px] font-bold text-gold-200">مساحة
                                رئيسية</span>
                            <h3 class="mt-5 text-2xl font-extrabold leading-9">ظهور بارز في الصفحة الرئيسية</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-100">بنر واسع مناسب لإطلاق العروض وتعزيز حضور العلامة.</p>
                            <a href="{{ $headerBanner?->link ? route('ads.click', $headerBanner) : route('contact') }}"
                                class="shine-cta mt-7 inline-flex h-11 items-center rounded-xl bg-gold-500 px-5 text-xs font-bold text-white transition hover:bg-gold-600">احجز
                                الآن</a>
                        </div>
                    </article>
                @endif

                @if ($homeBanner2?->image)
                    <a href="{{ route('ads.click', $homeBanner2) }}"
                        class="reveal-up group relative flex min-h-[300px] overflow-hidden rounded-3xl bg-gov-950 [animation-delay:80ms]">
                        <span
                            style="top: 12px; right: 12px; background-color: rgba(0,0,0,.4)"
                            class="absolute z-10 rounded-full px-2 py-1 text-[10px] font-bold text-white backdrop-blur-sm">إعلان</span>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner2->image) }}"
                            alt="{{ $homeBanner2->title }}" width="320" height="213" loading="lazy" decoding="async"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    </a>
                @else
                    <article
                        class="reveal-up group relative flex min-h-[300px] flex-col justify-end overflow-hidden rounded-3xl bg-gov-950 p-7 text-white [animation-delay:80ms]">
                        <img src="{{ asset('assets/images/ad-card-1.jpg') }}"
                            alt="" width="320" height="213" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(0,50,42,.22),rgba(0,50,42,.9))]"></div>
                        <div class="relative">
                            <span
                                class="rounded-full border border-gold-300/30 bg-white/10 px-3 py-1 text-[11px] font-bold text-gold-200">مساحة
                                متوسطة</span>
                            <h3 class="mt-5 text-2xl font-extrabold leading-9">وصول مستمر لجمهور مستهدف</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-100">مناسبة لشركات مواد البناء والمقاولين والتشطيبات.</p>
                            <a href="{{ $homeBanner2?->link ? route('ads.click', $homeBanner2) : route('contact') }}"
                                class="shine-cta mt-7 inline-flex h-11 items-center rounded-xl bg-gold-500 px-5 text-xs font-bold text-white transition hover:bg-gold-600">احجز
                                الآن</a>
                        </div>
                    </article>
                @endif

                @if ($homeBanner3?->image)
                    <a href="{{ route('ads.click', $homeBanner3) }}"
                        class="reveal-up group relative flex min-h-[300px] overflow-hidden rounded-3xl bg-gov-950 [animation-delay:160ms]">
                        <span
                            style="top: 12px; right: 12px; background-color: rgba(0,0,0,.4)"
                            class="absolute z-10 rounded-full px-2 py-1 text-[10px] font-bold text-white backdrop-blur-sm">إعلان</span>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homeBanner3->image) }}"
                            alt="{{ $homeBanner3->title }}" width="320" height="480" loading="lazy" decoding="async"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    </a>
                @else
                    <article
                        class="reveal-up group relative flex min-h-[300px] flex-col justify-end overflow-hidden rounded-3xl bg-gov-950 p-7 text-white [animation-delay:160ms]">
                        <img src="{{ asset('assets/images/ad-card-2.jpg') }}" alt="" width="320" height="480" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(0,50,42,.22),rgba(0,50,42,.9))]"></div>
                        <div class="relative">
                            <span
                                class="rounded-full border border-gold-300/30 bg-white/10 px-3 py-1 text-[11px] font-bold text-gold-200">مساحة
                                جانبية</span>
                            <h3 class="mt-5 text-2xl font-extrabold leading-9">حضور ثابت داخل صفحات الدليل</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-100">خيار عملي للظهور بجانب التصنيفات وصفحات الشركات.</p>
                            <a href="{{ $homeBanner3?->link ? route('ads.click', $homeBanner3) : route('contact') }}"
                                class="shine-cta mt-7 inline-flex h-11 items-center rounded-xl bg-gold-500 px-5 text-xs font-bold text-white transition hover:bg-gold-600">احجز
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
    <section class="bg-[#fbfcfb] py-6 sm:py-16">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex rounded-full bg-gov-50 px-3 py-1 text-[11px] font-bold text-gov-800">
                    شركاؤنا
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
                aria-label="شعارات الشركاء" x-data="{
                    logosPerView: 5,
                    logosClones: 5,
                    logosIndex: 0,
                    logosCardWidth: 200,
                    logosStep: 220,
                    logosInstant: false,
                    logosTimer: null,
                    logosBoundaryTimer: null,
                    logosDragging: false,
                    logosDragStartX: 0,
                    logosDragBaseIndex: 0,
                    logosDragOffsetPx: 0,
                    get logosExtended() {
                        const n = this.logosClones;
                        return [
                            ...partnerLogos.slice(-n).map((l, i) => ({ ...l, key: `pre-${i}` })),
                            ...partnerLogos.map((l, i) => ({ ...l, key: `real-${i}` })),
                            ...partnerLogos.slice(0, n).map((l, i) => ({ ...l, key: `post-${i}` })),
                        ];
                    },
                    get logosActiveDot() {
                        const len = partnerLogos.length;
                        return ((this.logosIndex % len) + len) % len;
                    },
                    get logosTrackStyle() {
                        const offset = -(this.logosClones + this.logosIndex) * this.logosStep + this.logosDragOffsetPx;
                        return `transform: translateX(${offset}px)`;
                    },
                    setLogosPerView() {
                        const w = window.innerWidth;
                        this.logosPerView = w < 736 ? 2 : w < 980 ? 3 : w < 1200 ? 4 : 5;
                        this.$nextTick(() => this.measureLogosStep());
                    },
                    measureLogosStep() {
                        const vp = this.$refs.logosViewport;
                        if (!vp) return;
                        const gap = 20;
                        const width = vp.getBoundingClientRect().width;
                        this.logosCardWidth = (width - gap * (this.logosPerView - 1)) / this.logosPerView;
                        this.logosStep = this.logosCardWidth + gap;
                    },
                    goToLogo(i) {
                        this.logosIndex = i;
                        this.restartLogosAutoplay();
                    },
                    restartLogosAutoplay() {
                        clearInterval(this.logosTimer);
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        if (partnerLogos.length <= 1) return;
                        this.logosTimer = setInterval(() => {
                            this.logosIndex++;
                            this.scheduleLogosBoundaryCheck();
                        }, 6000);
                    },
                    // A setTimeout matched to the track's 600ms CSS transition,
                    // not a `transitionend` listener: in testing, transitionend
                    // on this transform-only, will-change:transform track did
                    // not reliably fire (the transform still visibly animated —
                    // just no completion event), which left logosIndex counting
                    // up forever past the real item range instead of wrapping.
                    // A timer keyed to the known transition duration sidesteps
                    // that unreliability entirely.
                    scheduleLogosBoundaryCheck() {
                        clearTimeout(this.logosBoundaryTimer);
                        this.logosBoundaryTimer = setTimeout(() => {
                            const len = partnerLogos.length;
                            if (this.logosIndex >= len || this.logosIndex < 0) {
                                const normalized = ((this.logosIndex % len) + len) % len;
                                this.logosInstant = true;
                                this.logosIndex = normalized;
                                requestAnimationFrame(() => requestAnimationFrame(() => { this.logosInstant = false; }));
                            }
                        }, 650);
                    },
                    logosDragStart(e) {
                        clearInterval(this.logosTimer);
                        clearTimeout(this.logosBoundaryTimer);
                        this.logosDragging = true;
                        this.logosDragStartX = e.clientX;
                        this.logosDragBaseIndex = this.logosIndex;
                        this.measureLogosStep();
                        // Pointer capture keeps the drag tracking even if the
                        // pointer leaves the element mid-gesture; wrapped
                        // defensively since some browsers/input types can
                        // reject it for a pointerId that's already gone.
                        try { e.currentTarget.setPointerCapture(e.pointerId); } catch (err) {}
                    },
                    logosDragMove(clientX) {
                        if (!this.logosDragging) return;
                        this.logosDragOffsetPx = clientX - this.logosDragStartX;
                    },
                    logosDragEnd() {
                        if (!this.logosDragging) return;
                        this.logosDragging = false;
                        const deltaSteps = Math.round(this.logosDragOffsetPx / this.logosStep);
                        this.logosIndex = this.logosDragBaseIndex - deltaSteps;
                        this.logosDragOffsetPx = 0;
                        this.scheduleLogosBoundaryCheck();
                        this.restartLogosAutoplay();
                    },
                }" x-init="
                    setLogosPerView();
                    window.addEventListener('resize', () => setLogosPerView());
                    restartLogosAutoplay();
                " @focusin="clearInterval(logosTimer)" @focusout="restartLogosAutoplay()">

                {{-- Screen-reader list: the visual track duplicates logos for the
                     infinite-loop illusion and is hidden from assistive tech
                     (aria-hidden below) — this is the one real, non-duplicated
                     announcement of the partner list. --}}
                <ul class="sr-only">
                    <template x-for="logo in partnerLogos" :key="logo.name">
                        <li x-text="logo.name"></li>
                    </template>
                </ul>

                <div class="logos-carousel__viewport" x-ref="logosViewport" aria-hidden="true"
                    :class="{ 'is-dragging': logosDragging }"
                    @pointerdown="logosDragStart($event)"
                    @pointermove="logosDragMove($event.clientX)" @pointerup="logosDragEnd()"
                    @pointercancel="logosDragEnd()">
                    <div class="logos-carousel__track" :class="{ 'is-instant': logosInstant }" :style="logosTrackStyle">
                        <template x-for="item in logosExtended" :key="item.key">
                            <div class="logos-carousel__card" :style="`width:${logosCardWidth}px`">
                                <img :src="item.logo" :alt="item.name" width="140" height="57" loading="lazy"
                                    decoding="async" draggable="false">
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center gap-2">
                    <template x-for="(logo, i) in partnerLogos" :key="i">
                        <button type="button" class="logos-carousel__dot" :class="{ 'is-active': logosActiveDot === i }"
                            @click="goToLogo(i)" :aria-label="`الانتقال إلى ${logo.name}`"
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
    document.addEventListener('alpine:init', () => {
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
            selectedNews: {},
            toast: '',
            toastTimer: null,
            officialEntities: [
                {
                    name: 'وزارة البلديات والإسكان',
                    logo: '{{ asset('assets/entities/1.png') }}'
                },
                {
                    name: 'وزارة الصناعة والثروة المعدنية',
                    logo: '{{ asset('assets/entities/2.png') }}'
                },
                {
                    name: 'الهيئة السعودية للمقاولين',
                    logo: '{{ asset('assets/entities/3.png') }}'
                },
                {
                    name: 'غرفة الرياض',
                    logo: '{{ asset('assets/entities/4.png') }}'
                },
                {
                    name: 'هيئة المحتوى المحلي والمشتريات الحكومية',
                    logo: '{{ asset('assets/entities/5.png') }}'
                },
                {
                    name: 'منصة اعتماد',
                    logo: '{{ asset('assets/entities/6.png') }}'
                }
            ],
            // Single source of truth for the "Partner Logos" carousel below —
            // admin-managed via Filament (المحتوى → شعارات الشركاء) and
            // queried in HomeController, not hardcoded here anymore.
            partnerLogos: @json($dbPartnerLogos),
            nav: [
                { id: 'home', label: 'الرئيسية' },
                { id: 'directory', label: 'الشركات' },
                { id: 'categories', label: 'التصنيفات' },
                { id: 'news', label: 'الأخبار' },
                { id: 'about', label: 'من نحن' },
                { id: 'contact', label: 'تواصل معنا' }
            ],
            cities: @json($dbCities),
            categories: @json($dbCategories),
            companies: @json($dbCompanies),
            news: @json($dbNews),
            init() {
                this.route = this.routeFromPath();
                this.selectedNews = this.news[0];
                this.updateStickyNav();
                window.addEventListener('scroll', () => this.updateStickyNav(), { passive: true });
                window.addEventListener('resize', () => this.updateStickyNav());
            },
            updateStickyNav() {
                const nav = document.getElementById('site-sticky-nav');
                this.navStuck = window.scrollY >= ((nav?.offsetTop || 0) - 1);
            },
            routeFromPath() {
                const path = location.pathname;
                const map = {
                    '{{ parse_url(route('home'), PHP_URL_PATH) ?: '/' }}': 'home',
                    '{{ parse_url(route('companies.index'), PHP_URL_PATH) }}': 'directory',
                    '{{ parse_url(route('news.index'), PHP_URL_PATH) }}': 'news',
                    '{{ parse_url(route('about'), PHP_URL_PATH) }}': 'about',
                    '{{ parse_url(route('contact'), PHP_URL_PATH) }}': 'contact',
                    '{{ parse_url(route('plans'), PHP_URL_PATH) }}': 'plans',
                };
                return map[path] || 'home';
            },
            go(r) {
                if (r === 'categories') { document.getElementById('companies')?.scrollIntoView({ behavior: 'smooth' }); return }
                const paths = {
                    home: '{{ route('home') }}',
                    directory: '{{ route('companies.index') }}',
                    news: '{{ route('news.index') }}',
                    about: '{{ route('about') }}',
                    contact: '{{ route('contact') }}',
                    plans: '{{ route('plans') }}',
                };
                location.href = paths[r] || '{{ route('home') }}';
            },
            openNews(item) {
                location.href = '{{ route('news.show', ['slug' => '__SLUG__']) }}'.replace('__SLUG__', encodeURIComponent(item.slug));
            },
            searchFromHero() {
                const params = new URLSearchParams();
                if (this.heroSearch.trim()) params.set('q', this.heroSearch.trim());
                if (this.heroCity !== 'جميع المدن') params.set('city', this.heroCity);
                location.href = `{{ route('companies.index') }}${params.toString() ? `?${params.toString()}` : ''}`;
            },
            selectCategoryAndGo(category) {
                location.href = `{{ route('companies.index') }}?sector=${encodeURIComponent(category)}`;
            },
            resetFilters() {
                this.selectedCategory = 'جميع الشركات';
                this.selectedCity = 'اختيار المدينة';
                this.sortBy = 'ترتيب افتراضي';
                this.companySearch = '';
            },
            showToast(message) {
                this.toast = message;
                clearTimeout(this.toastTimer);
                this.toastTimer = setTimeout(() => this.toast = '', 3200);
            },
            get filteredCompanies() {
                const results = this.companies.filter(company => {
                    const byCategory = this.selectedCategory === 'جميع الشركات' || company.category === this.selectedCategory;
                    const byCity = this.selectedCity === 'اختيار المدينة' || this.selectedCity === 'جميع المدن' || company.city === this.selectedCity;
                    const search = this.companySearch.trim();
                    const bySearch = !search || company.name.includes(search);
                    return byCategory && byCity && bySearch;
                });
                if (this.sortBy === 'حسب المدينة') return results.sort((a, b) => a.city.localeCompare(b.city, 'ar'));
                if (this.sortBy === 'الأحدث') return results.slice().reverse();
                return results;
            }
        }))
    })
</script>
@endpush
