@php($seoTitle = 'أخبار توريد')
@php($seoDescription = \App\Models\SiteSetting::get(
    'default_seo_description',
    'أخبار قطاع البناء وشركات البناء والموردين في المملكة العربية السعودية عبر توريد.'
))
@php($alpineComponent = 'newsPage')
@extends('layouts.app')

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
                    <span>الأخبار</span>
                </div>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight sm:text-5xl">أخبار قطاع البناء</h1>
                <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-100 sm:text-base">
                    آخر التحديثات والعروض والأخبار المرتبطة بشركات البناء والموردين في المملكة العربية السعودية.
                </p>
            </div>
        </div>
    </section>

    <!-- All News -->
    <section class="pb-16 pt-6 [contain-intrinsic-size:900px] [content-visibility:auto]">
        <div class="mx-auto grid max-w-[1500px] gap-8 px-4 sm:px-6 lg:grid-cols-[300px_1fr] lg:px-8">
            <div class="lg:col-start-2 lg:row-start-1">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-extrabold text-gov-950">كل الأخبار</h2>
                    <form method="GET" action="{{ route('news.index') }}"
                        class="flex h-12 w-full items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 transition focus-within:border-gold-400 focus-within:ring-2 focus-within:ring-gold-100 sm:w-[320px]">
                        <label for="news-search" class="sr-only">بحث في الأخبار</label>
                        <button type="submit" class="shrink-0 text-slate-400 transition hover:text-gov-700" aria-label="بحث">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-4-4" />
                            </svg>
                        </button>
                        <input id="news-search" name="q" type="search" value="{{ $search }}"
                            class="h-full w-full bg-transparent text-xs outline-none placeholder:text-slate-500" aria-describedby="news-results-status"
                            placeholder="بحث في الأخبار">
                        @if ($activeCategorySlug !== '')
                            <input type="hidden" name="category" value="{{ $activeCategorySlug }}">
                        @endif
                    </form>
                </div>

                <p id="news-results-status" class="sr-only" aria-live="polite">
                    عدد الأخبار المطابقة: {{ $news->total() }}
                    @if ($activeCategoryName) ضمن {{ $activeCategoryName }} @endif
                    @if ($search !== '') عن "{{ $search }}" @endif
                </p>

                <div
                    class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="leading-7 text-slate-700">
                        <span>عدد الأخبار المطابقة:</span>
                        <span class="font-extrabold text-gov-950">{{ $news->total() }}</span>
                        @if ($activeCategoryName)
                            <span>
                                <span>ضمن</span>
                                <strong class="font-bold text-gov-900">{{ $activeCategoryName }}</strong>
                            </span>
                        @endif
                        @if ($search !== '')
                            <span>
                                <span>عن</span>
                                <strong class="font-bold text-gov-900">"{{ $search }}"</strong>
                            </span>
                        @endif
                    </p>
                    @if ($activeCategorySlug !== '' || $search !== '')
                        <a href="{{ route('news.index') }}"
                            class="self-start rounded-xl border border-gov-100 bg-gov-50 px-4 py-2 text-xs font-bold text-gov-900 transition hover:border-gov-200 hover:bg-gov-100 sm:self-auto">
                            عرض كل الأخبار
                        </a>
                    @endif
                </div>

                @if ($news->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($news as $item)
                            <article
                                class="touch-static overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:border-gov-200 hover:shadow-[0_6px_14px_rgba(7,30,23,0.06)]">
                                <img src="{{ $item->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->image) : asset('assets/images/news-placeholder.jpg') }}"
                                    alt="{{ $item->title }}" width="640" height="427"
                                    loading="lazy" decoding="async" class="h-40 w-full object-cover">
                                <div class="p-4">
                                    <span class="rounded-full bg-gov-50 px-3 py-1 text-[10px] font-bold text-gov-800">{{ $item->categoryRelation?->name }}</span>
                                    <h3 class="mt-4 line-clamp-2 text-base font-extrabold leading-6 text-gov-950">{{ $item->title }}</h3>
                                    <p class="mt-3 line-clamp-2 text-xs leading-5 text-slate-500">{{ $item->excerpt }}</p>
                                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                        <time class="text-[11px] font-medium text-slate-500" datetime="{{ $item->published_at->toIso8601String() }}">{{ $item->published_at->locale('ar')->translatedFormat('d F Y') }}</time>
                                        <a href="{{ route('news.show', $item->slug) }}?from={{ urlencode(request()->fullUrl()) }}"
                                            aria-label="قراءة المزيد: {{ $item->title }}"
                                            class="text-xs font-bold text-gov-800 hover:text-gold-700">
                                            قراءة المزيد
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-14 text-center">
                        <h3 class="font-extrabold text-gov-900">لا توجد أخبار بهذه المعايير</h3>
                        <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-600">
                            جرّب إزالة عبارة البحث أو اختر تصنيف "الكل" لعرض آخر الأخبار المتاحة.
                        </p>
                        <a href="{{ route('news.index') }}"
                            class="mt-5 inline-flex rounded-xl bg-gov-800 px-5 py-3 text-xs font-bold text-white">عرض كل الأخبار</a>
                    </div>
                @endif

                @if ($news->hasPages())
                    <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="ترقيم صفحات الأخبار">
                        @if ($news->onFirstPage())
                            <span class="inline-flex h-11 min-w-24 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-400 opacity-45">السابق</span>
                        @else
                            <a href="{{ $news->previousPageUrl() }}"
                                class="inline-flex h-11 min-w-24 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">السابق</a>
                        @endif

                        @foreach ($news->getUrlRange(1, $news->lastPage()) as $page => $url)
                            @if ($page === $news->currentPage())
                                <span aria-current="page"
                                    class="grid h-11 w-11 place-items-center rounded-2xl bg-gov-800 text-sm font-bold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 transition hover:border-gov-300">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($news->hasMorePages())
                            <a href="{{ $news->nextPageUrl() }}"
                                class="inline-flex h-11 min-w-24 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">التالي</a>
                        @else
                            <span class="inline-flex h-11 min-w-24 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-400 opacity-45">التالي</span>
                        @endif
                    </nav>
                @endif
            </div>

            <aside class="space-y-5 lg:col-start-1 lg:row-start-1 lg:sticky lg:top-24">
                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <h3 class="font-extrabold text-gov-950">تصنيفات الأخبار</h3>
                    <div class="mt-5 space-y-2">
                        <a href="{{ route('news.index', array_filter(['q' => $search ?: null])) }}"
                            aria-pressed="{{ $activeCategorySlug === '' ? 'true' : 'false' }}"
                            aria-label="{{ $activeCategorySlug === '' ? 'محدد حالياً' : 'عرض' }}: الكل، {{ $totalPublished }} خبر"
                            class="flex w-full items-center justify-between rounded-2xl px-3 py-3 text-right text-xs font-bold transition {{ $activeCategorySlug === '' ? 'border border-gov-100 bg-gov-50 text-gov-900' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span>الكل</span>
                            <span class="text-slate-600" aria-hidden="true">{{ $totalPublished }}</span>
                        </a>
                        @foreach ($categories as $categoryRow)
                            <a href="{{ route('news.index', array_filter(['q' => $search ?: null, 'category' => $categoryRow->slug])) }}"
                                aria-pressed="{{ $activeCategorySlug === $categoryRow->slug ? 'true' : 'false' }}"
                                aria-label="{{ $activeCategorySlug === $categoryRow->slug ? 'محدد حالياً' : 'عرض' }}: {{ $categoryRow->name }}، {{ $categoryRow->news_count }} خبر"
                                class="flex w-full items-center justify-between rounded-2xl px-3 py-3 text-right text-xs font-bold transition {{ $activeCategorySlug === $categoryRow->slug ? 'border border-gov-100 bg-gov-50 text-gov-900' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span>{{ $categoryRow->name }}</span>
                                <span class="text-slate-600" aria-hidden="true">{{ $categoryRow->news_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl bg-gov-950 p-6 text-white">
                    <p class="text-xs font-bold text-gold-300">أعلن في توريد</p>
                    <h3 class="mt-3 text-2xl font-extrabold leading-9">اعرض شركتك أمام الباحثين عن شركات البناء</h3>
                    <a href="{{ route('contact') }}"
                        class="mt-6 inline-flex h-12 items-center justify-center rounded-2xl bg-gold-500 px-6 text-xs font-bold text-white transition hover:bg-gold-600">
                        احجز إعلانك
                    </a>
                </div>
            </aside>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('newsPage', () => ({
            // Shared header/footer state
            route: 'news',
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
            },
            selectCategoryAndGo(category) {
                location.href = `{{ route('companies.index') }}?sector=${encodeURIComponent(category)}`;
            }
        }))
    })
</script>
@endpush
