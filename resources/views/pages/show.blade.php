@extends('layouts.app', ['alpineComponent' => 'cmsPage'])

@section('content')

    <!-- Page Hero -->
    <section class="relative flex min-h-[280px] items-center overflow-hidden bg-gov-950 py-14 text-white sm:py-16">
        <img src="{{ asset('assets/images/hero-construction-1200.jpg') }}"
            srcset="{{ asset('assets/images/hero-construction-768.jpg') }} 768w, {{ asset('assets/images/hero-construction-1200.jpg') }} 1200w, {{ asset('assets/images/hero-construction.jpg') }} 1600w"
            sizes="100vw" alt="" width="1600" height="900" decoding="async" fetchpriority="high"
            class="absolute inset-0 h-full w-full object-cover opacity-25">
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,50,42,.95),rgba(0,58,48,.88),rgba(0,50,42,.95))]"></div>
        <div class="relative w-full container-page">
            <div class="max-w-3xl">
                <nav class="flex items-center gap-2 text-xs font-bold text-gold-200" aria-label="مسار الصفحة">
                    <a href="{{ route('home') }}" class="transition hover:text-white">الرئيسية</a>
                    <span class="text-white/40">/</span>
                    <span aria-current="page">{{ $page->title }}</span>
                </nav>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight sm:text-5xl">
                    {{ $page->hero_title ?: $page->title }}
                </h1>
                @if ($page->hero_description || $page->excerpt)
                    <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-100 sm:text-base">
                        {{ $page->hero_description ?: $page->excerpt }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <section class="py-14 sm:py-16">
        <div class="container-page">
            <div class="mx-auto max-w-4xl">
                @if (filled($page->content))
                    <div class="card-soft p-6 sm:p-9">
                        <div class="mx-auto max-w-none text-sm leading-8 text-slate-700 sm:text-base sm:leading-9
                            [&>*+*]:mt-5 [&_h2]:text-xl [&_h2]:font-extrabold [&_h2]:leading-8 [&_h2]:text-gov-950
                            [&_h3]:text-lg [&_h3]:font-bold [&_h3]:leading-7 [&_h3]:text-gov-950
                            [&_a]:font-semibold [&_a]:text-gov-700 [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-gov-900
                            [&_strong]:font-bold [&_strong]:text-gov-950
                            [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:pr-5 [&_ol]:list-decimal [&_ol]:space-y-2 [&_ol]:pr-5
                            [&_blockquote]:rounded-xl [&_blockquote]:border-r-4 [&_blockquote]:border-gold-300 [&_blockquote]:bg-gold-50 [&_blockquote]:px-4 [&_blockquote]:py-3 [&_blockquote]:text-slate-600"
                            style="overflow-wrap: anywhere;">
                            {!! $page->content !!}
                        </div>
                    </div>
                @else
                    <div class="card-soft px-5 py-14 text-center">
                        <p class="text-xs font-bold text-gold-700">{{ $page->title }}</p>
                        <h2 class="mt-3 text-xl font-extrabold text-gov-950">لا يوجد محتوى لهذه الصفحة بعد</h2>
                        <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-500">
                            سيتم إضافة محتوى هذه الصفحة قريباً من لوحة التحكم.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        const homeUrl = @js(route('home'));
        const contactUrl = @js(route('contact'));
        const companiesUrl = @js(route('companies.index'));
        const aboutUrl = @js(route('about'));
        const plansUrl = @js(route('plans'));
        const newsIndexUrl = @js(route('news.index'));
        const activeRoute = @js(request()->routeIs('about') ? 'about' : '');

        Alpine.data('cmsPage', () => ({
            // Shared header/footer state
            route: activeRoute,
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
                    location.href = `${homeUrl}#companies`;
                    return;
                }
                const paths = {
                    home: homeUrl,
                    directory: companiesUrl,
                    news: newsIndexUrl,
                    about: aboutUrl,
                    contact: contactUrl,
                    plans: plansUrl,
                };
                location.href = paths[routeId] || homeUrl;
            },
            selectCategoryAndGo(category) {
                location.href = `${companiesUrl}?sector=${encodeURIComponent(category)}`;
            }
        }));
    });
</script>
@endpush
