@php($seoTitle = $news->title)
@php($seoDescription = $news->excerpt ?: $news->title)
@php($seoImage = $news->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($news->image) : null)
@extends('layouts.app', ['alpineComponent' => 'newsShowPage'])

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/images/hero-construction-1200.jpg') }}" fetchpriority="high">
@endpush

@section('content')

    <article aria-labelledby="article-title">
        <header class="relative overflow-hidden bg-gov-950 py-14 text-white sm:py-16">
            <img src="{{ asset('assets/images/hero-construction-1200.jpg') }}"
                srcset="{{ asset('assets/images/hero-construction-768.jpg') }} 768w, {{ asset('assets/images/hero-construction-1200.jpg') }} 1200w, {{ asset('assets/images/hero-construction.jpg') }} 1600w"
                sizes="100vw" alt="" width="1600" height="900" decoding="async" fetchpriority="high"
                class="absolute inset-0 h-full w-full object-cover opacity-25">
            <div
                class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,50,42,.95),rgba(0,58,48,.88),rgba(0,50,42,.95))]">
            </div>
            <div class="relative mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <nav class="flex flex-wrap items-center gap-2 text-xs font-bold text-gold-200"
                        aria-label="مسار الخبر">
                        <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center transition hover:text-white">الرئيسية</a>
                        <span class="text-white/40">/</span>
                        <a href="{{ $backUrl }}" class="inline-flex min-h-10 items-center transition hover:text-white">الأخبار</a>
                        @if ($news->categoryRelation)
                            <span class="text-white/40">/</span>
                            <span>{{ $news->categoryRelation->name }}</span>
                        @endif
                    </nav>
                    @if ($news->categoryRelation)
                        <span class="mt-7 inline-flex rounded-full bg-gold-400 px-4 py-2 text-xs font-bold text-gov-950">{{ $news->categoryRelation->name }}</span>
                    @endif
                    <h1 id="article-title" class="mt-5 max-w-4xl text-3xl font-extrabold leading-tight sm:text-4xl"
                        style="text-wrap: balance; overflow-wrap: anywhere;">{{ $news->title }}</h1>
                    <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-slate-100">
                        <time class="font-bold text-gold-200" datetime="{{ $news->published_at->toIso8601String() }}">{{ $news->published_at->locale('ar')->translatedFormat('d F Y') }}</time>
                        <span class="hidden text-white/30 sm:inline">|</span>
                        <span>قراءة عملية لقطاع البناء والتوريد</span>
                    </div>
                    @if ($news->excerpt)
                        <p class="mt-5 max-w-3xl text-base leading-9 text-slate-100" style="text-wrap: pretty;">{{ $news->excerpt }}</p>
                    @endif
                    <a href="{{ $backUrl }}"
                        class="mt-8 inline-flex min-h-12 items-center justify-center rounded-xl border border-gold-300 px-5 text-sm font-bold text-gold-200 transition hover:bg-gold-400 hover:text-gov-950">
                        العودة للأخبار
                    </a>
                </div>
            </div>
        </header>

        <section class="py-12 sm:py-16">
            <div class="mx-auto grid max-w-[1500px] gap-8 px-4 sm:px-6 lg:grid-cols-[1fr_360px] lg:px-8">
                <div class="min-w-0">
                    <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <img src="{{ $news->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($news->image) : asset('assets/images/hero-construction-1200.jpg') }}"
                            sizes="(min-width: 1024px) 920px, 100vw" alt="{{ $news->title }}"
                            width="960" height="640" loading="lazy" decoding="async"
                            class="h-64 w-full object-cover sm:h-72">
                        <figcaption class="border-t border-slate-100 px-5 py-4 text-xs leading-6 text-slate-500">
                            {{ $news->categoryRelation?->name }}
                            @if ($news->categoryRelation) &middot; @endif
                            {{ $news->published_at->locale('ar')->translatedFormat('d F Y') }}
                        </figcaption>
                    </figure>

                    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9">
                        <div class="mx-auto max-w-3xl" style="max-width: 70ch; overflow-wrap: anywhere;">
                            <section aria-labelledby="content-heading">
                                <h2 id="content-heading" class="text-xl font-extrabold leading-8 text-gov-950">تفاصيل الخبر</h2>

                                @if (filled($news->content))
                                    <div class="mt-4 text-base leading-9 text-slate-700
                                        [&>*+*]:mt-5 [&_h2]:text-xl [&_h2]:font-extrabold [&_h2]:leading-8 [&_h2]:text-gov-950
                                        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:leading-7 [&_h3]:text-gov-950
                                        [&_a]:font-semibold [&_a]:text-gov-700 [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-gov-900
                                        [&_strong]:font-bold [&_strong]:text-gov-950
                                        [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:pr-5 [&_ol]:list-decimal [&_ol]:space-y-2 [&_ol]:pr-5
                                        [&_blockquote]:rounded-xl [&_blockquote]:border-r-4 [&_blockquote]:border-gold-300 [&_blockquote]:bg-gold-50 [&_blockquote]:px-4 [&_blockquote]:py-3 [&_blockquote]:text-slate-600"
                                        style="text-wrap: pretty;">
                                        {!! $news->content !!}
                                    </div>
                                @elseif (filled($news->excerpt))
                                    <p class="mt-4 text-lg leading-9 text-slate-700" style="text-wrap: pretty;">{{ $news->excerpt }}</p>
                                    <p class="mt-4 text-sm text-slate-500">لا يتوفر محتوى إضافي لهذا الخبر حالياً.</p>
                                @else
                                    <p class="mt-4 text-sm text-slate-500">لا يتوفر محتوى لهذا الخبر حالياً.</p>
                                @endif
                            </section>

                            <div class="mt-10 flex flex-col rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                                <div>
                                    <h2 class="text-lg font-extrabold text-gov-950">هل تبحث عن موردين مرتبطين بهذا الخبر؟</h2>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">
                                        انتقل إلى دليل الشركات للبحث حسب التصنيف أو المدينة ومقارنة الموردين المناسبين.
                                    </p>
                                </div>
                                <a href="{{ route('companies.index', array_filter(['sector' => $news->categoryRelation?->name])) }}"
                                    class="mt-5 inline-flex min-h-12 items-center justify-center rounded-xl bg-gov-800 px-5 text-sm font-bold text-white transition hover:bg-gov-700">
                                    استعرض الشركات
                                </a>
                            </div>

                            @if ($newsFooterBanner?->image)
                                <a href="{{ route('ads.click', $newsFooterBanner) }}"
                                    class="mt-6 block overflow-hidden rounded-2xl border border-slate-200 shadow-sm transition hover:border-gold-300 hover:shadow-md"
                                    aria-label="{{ $newsFooterBanner->title ?: 'مساحة إعلانية' }}">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($newsFooterBanner->image) }}"
                                        width="728" height="200" alt="{{ $newsFooterBanner->title ?: 'مساحة إعلانية' }}"
                                        loading="lazy" decoding="async" class="h-auto w-full object-cover">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="space-y-5 lg:sticky lg:top-24" aria-label="محتوى مرتبط">
                    @if ($newsSidebarBanner?->image)
                        <a href="{{ route('ads.click', $newsSidebarBanner) }}"
                            class="block overflow-hidden rounded-2xl border border-slate-200 shadow-sm transition hover:border-gold-300 hover:shadow-md"
                            aria-label="{{ $newsSidebarBanner->title ?: 'مساحة إعلانية' }}">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($newsSidebarBanner->image) }}"
                                width="320" height="480" alt="{{ $newsSidebarBanner->title ?: 'مساحة إعلانية' }}"
                                loading="lazy" decoding="async" class="h-auto w-full object-cover">
                        </a>
                    @endif

                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-base font-extrabold text-gov-950">أخبار ذات صلة</h2>
                        <div class="mt-5 space-y-4">
                            @forelse ($relatedNews as $related)
                                <a href="{{ route('news.show', $related->slug) }}"
                                    class="block min-h-12 w-full border-b border-slate-100 py-3 text-right last:border-0"
                                    aria-label="فتح خبر: {{ $related->title }}">
                                    <span class="text-[11px] font-bold text-gold-700">{{ $related->categoryRelation?->name }}</span>
                                    <span class="mt-1 block text-sm font-bold leading-6 text-gov-950">{{ $related->title }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $related->published_at->locale('ar')->translatedFormat('d F Y') }}</span>
                                </a>
                            @empty
                                <p class="text-xs leading-6 text-slate-500">لا توجد أخبار ذات صلة حالياً.</p>
                            @endforelse
                        </div>
                    </section>

                    @if ($categories->isNotEmpty())
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-base font-extrabold text-gov-950">أحدث التصنيفات</h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($categories as $categoryRow)
                                    <a href="{{ route('news.index', ['category' => $categoryRow->slug]) }}"
                                        class="inline-flex min-h-10 items-center rounded-full border border-slate-200 px-3 text-xs font-bold text-slate-700 transition hover:border-gold-300 hover:text-gov-900">
                                        {{ $categoryRow->name }}
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="rounded-2xl border border-gold-200 bg-gold-50 p-5">
                        <h2 class="text-base font-extrabold text-gov-950">تحديثات توريد</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-700">
                            احصل على إشعار عند نشر أخبار جديدة عن الموردين والأسواق ومشروعات البناء.
                        </p>
                        <button @click="showToast('تم تسجيل اهتمامك بتحديثات الأخبار')"
                            class="mt-5 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-gov-800 px-5 text-sm font-bold text-white transition hover:bg-gov-700">
                            اشترك في التحديثات
                        </button>
                    </section>
                </aside>
            </div>
        </section>
    </article>

    <div x-show="toast" x-cloak x-transition role="status" aria-live="polite" class="fixed bottom-6 right-1/2 z-[70] w-[calc(100%-2rem)] max-w-md translate-x-1/2 rounded-2xl border border-gold-300 bg-gov-950 px-5 py-4 text-center text-xs font-semibold text-white shadow-2xl" x-text="toast"></div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('newsShowPage', () => ({
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

            // Page-specific state
            toast: '',
            toastTimer: null,

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
            },
            showToast(message) {
                this.toast = message || 'تم تنفيذ الإجراء';
                clearTimeout(this.toastTimer);
                this.toastTimer = setTimeout(() => { this.toast = ''; }, 3200);
            },
            destroy() {
                clearTimeout(this.toastTimer);
            }
        }));
    });
</script>
@endpush
