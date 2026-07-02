@php($title = 'الأخبار | توريدات')
@php($description = 'أخبار قطاع البناء وشركات البناء والموردين في المملكة العربية السعودية عبر توريدات.')
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
                    <div
                        class="flex h-12 w-full items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 transition focus-within:border-gold-400 focus-within:ring-2 focus-within:ring-gold-100 sm:w-[320px]">
                        <label for="news-search" class="sr-only">بحث في الأخبار</label>
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-4-4" />
                        </svg>
                        <input id="news-search" x-model="newsSearch" @input="currentPage=1"
                            class="h-full w-full bg-transparent text-xs outline-none placeholder:text-slate-500" aria-describedby="news-results-status"
                            placeholder="بحث في الأخبار">
                    </div>
                </div>

                <p id="news-results-status" class="sr-only" aria-live="polite"
                    x-text="newsResultsAnnouncement"></p>

                <div
                    class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="leading-7 text-slate-700">
                        <span>عدد الأخبار المطابقة:</span>
                        <span class="font-extrabold text-gov-950" x-text="filteredNews.length"></span>
                        <template x-if="selectedCategory !== 'الكل'">
                            <span>
                                <span>ضمن</span>
                                <strong class="font-bold text-gov-900" x-text="selectedCategory"></strong>
                            </span>
                        </template>
                        <template x-if="newsSearch.trim()">
                            <span>
                                <span>عن</span>
                                <strong class="font-bold text-gov-900" x-text="`“${newsSearch.trim()}”`"></strong>
                            </span>
                        </template>
                    </p>
                    <button x-show="hasActiveNewsFilter" x-cloak @click="resetNewsFilters()"
                        class="self-start rounded-xl border border-gov-100 bg-gov-50 px-4 py-2 text-xs font-bold text-gov-900 transition hover:border-gov-200 hover:bg-gov-100 sm:self-auto">
                        عرض كل الأخبار
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <template x-for="item in paginatedNews" :key="item.slug">
                        <article
                            class="touch-static overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:border-gov-200 hover:shadow-[0_6px_14px_rgba(7,30,23,0.06)]">
                            <img :src="item.image" :srcset="imageSrcset(item.image)"
                                sizes="(min-width: 1280px) 25vw, (min-width: 768px) 50vw, 100vw" :alt="item.title" width="640" height="427"
                                loading="lazy" decoding="async" class="h-40 w-full object-cover">
                            <div class="p-4">
                                <span class="rounded-full bg-gov-50 px-3 py-1 text-[10px] font-bold text-gov-800"
                                    x-text="item.category"></span>
                                <h3 class="mt-4 line-clamp-2 text-base font-extrabold leading-6 text-gov-950"
                                    x-text="item.title"></h3>
                                <p class="mt-3 line-clamp-2 text-xs leading-5 text-slate-500" x-text="item.summary"></p>
                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                    <span class="text-[11px] font-medium text-slate-500" x-text="item.date"></span>
                                    <button @click="openNews(item)" :aria-label="`قراءة المزيد: ${item.title}`"
                                        class="text-xs font-bold text-gov-800 hover:text-gold-700">
                                        قراءة المزيد
                                    </button>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>

                <div x-show="filteredNews.length===0" x-cloak
                    class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-14 text-center">
                    <h3 class="font-extrabold text-gov-900">لا توجد أخبار بهذه المعايير</h3>
                    <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-600">
                        جرّب إزالة عبارة البحث أو اختر تصنيف "الكل" لعرض آخر الأخبار المتاحة.
                    </p>
                    <button @click="resetNewsFilters()"
                        class="mt-5 rounded-xl bg-gov-800 px-5 py-3 text-xs font-bold text-white">عرض كل الأخبار</button>
                </div>

                <nav x-show="totalPages>1" x-cloak class="mt-10 flex flex-wrap items-center justify-center gap-2"
                    aria-label="ترقيم صفحات الأخبار">
                    <button @click="previousPage()" :disabled="currentPage===1"
                        class="h-11 min-w-24 rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900 disabled:cursor-not-allowed disabled:opacity-45">السابق</button>
                    <template x-for="page in pageNumbers" :key="page">
                        <button @click="setPage(page)" :aria-current="currentPage===page ? 'page' : null"
                            class="grid h-11 w-11 place-items-center rounded-2xl text-sm font-bold transition"
                            :class="currentPage===page ? 'bg-gov-800 text-white' : 'border border-slate-200 text-slate-600 hover:border-gov-300'">
                            <span x-text="page"></span>
                        </button>
                    </template>
                    <button @click="nextPage()" :disabled="currentPage===totalPages"
                        class="h-11 min-w-24 rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900 disabled:cursor-not-allowed disabled:opacity-45">التالي</button>
                </nav>
            </div>

            <aside class="space-y-5 lg:col-start-1 lg:row-start-1 lg:sticky lg:top-24">
                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <h3 class="font-extrabold text-gov-950">تصنيفات الأخبار</h3>
                    <div class="mt-5 space-y-2">
                        <template x-for="category in categories" :key="category">
                            <button @click="selectedCategory=category;currentPage=1" :aria-pressed="selectedCategory===category"
                                :aria-label="categoryFilterLabel(category)"
                                class="flex w-full items-center justify-between rounded-2xl px-3 py-3 text-right text-xs font-bold transition"
                                :class="selectedCategory===category ? 'border border-gov-100 bg-gov-50 text-gov-900' : 'text-slate-600 hover:bg-slate-50'">
                                <span x-text="category"></span>
                                <span class="text-slate-600" aria-hidden="true" x-text="countByCategory(category)"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl bg-gov-950 p-6 text-white">
                    <p class="text-xs font-bold text-gold-300">أعلن في توريدات</p>
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
        const newsShowBase = @js(route('news.show', 'sample'));

        Alpine.data('newsPage', () => ({
            route: 'news',
            mobile: false,
            navStuck: false,
            selectedCategory: 'الكل',
            newsSearch: '',
            currentPage: 1,
            pageSize: 9,
            urlSyncReady: false,
            nav: [
                { id: 'home', label: 'الرئيسية' },
                { id: 'directory', label: 'الشركات' },
                { id: 'categories', label: 'التصنيفات' },
                { id: 'news', label: 'الأخبار' },
                { id: 'about', label: 'من نحن' },
                { id: 'contact', label: 'تواصل معنا' }
            ],
            categories: ['الكل', 'أخبار السوق', 'مشاريع', 'عروض الشركات', 'تحديثات توريدات'],
            news: [
                {
                    slug: 'materials-prices-update',
                    title: 'تحديث أسعار بعض مواد البناء هذا الأسبوع',
                    category: 'أخبار السوق',
                    date: '26 يونيو 2026',
                    summary: 'متابعة مختصرة لأبرز التغييرات في أسعار مواد البناء لدى عدد من الموردين.',
                    image: '{{ asset('assets/images/news-1.jpg') }}'
                },
                {
                    slug: 'ready-mix-demand',
                    title: 'طلب متزايد على موردي الخرسانة في المدن الكبرى',
                    category: 'مشاريع',
                    date: '24 يونيو 2026',
                    summary: 'تشهد المشاريع السكنية والتجارية طلباً متزايداً على الخرسانة الجاهزة وخدمات الصب.',
                    image: '{{ asset('assets/images/news-2.jpg') }}'
                },
                {
                    slug: 'finishing-offers',
                    title: 'عروض جديدة من شركات الدهانات والتشطيبات',
                    category: 'عروض الشركات',
                    date: '22 يونيو 2026',
                    summary: 'عدد من الموردين يطلقون عروضاً موسمية على مواد التشطيب والدهانات الداخلية والخارجية.',
                    image: '{{ asset('assets/images/news-3.jpg') }}'
                },
                {
                    slug: 'platform-categories-update',
                    title: 'إضافة تصنيفات جديدة داخل دليل توريدات',
                    category: 'تحديثات توريدات',
                    date: '20 يونيو 2026',
                    summary: 'تحديثات جديدة تساعد الزوار على الوصول إلى الشركات حسب الخدمة والمدينة بشكل أسرع.',
                    image: '{{ asset('assets/images/news-4.jpg') }}'
                },
                {
                    slug: 'steel-market-note',
                    title: 'ملاحظات سريعة حول سوق الحديد والصلب',
                    category: 'أخبار السوق',
                    date: '18 يونيو 2026',
                    summary: 'قراءة مختصرة لاتجاهات توريد الحديد ومنتجات الصلب في مشاريع البناء.',
                    image: '{{ asset('assets/images/news-5.jpg') }}'
                },
                {
                    slug: 'supplier-profile-tips',
                    title: 'كيف تعرض شركتك بشكل أفضل داخل الدليل؟',
                    category: 'تحديثات توريدات',
                    date: '15 يونيو 2026',
                    summary: 'نصائح عملية لتحسين ظهور شركة البناء أو المورد داخل صفحات الدليل.',
                    image: '{{ asset('assets/images/news-6.jpg') }}'
                },
                {
                    slug: 'site-safety-supplies',
                    title: 'زيادة الطلب على مستلزمات السلامة في مواقع البناء',
                    category: 'أخبار السوق',
                    date: '12 يونيو 2026',
                    summary: 'موردون يسجلون طلباً أعلى على معدات الوقاية وحلول السلامة مع توسع المشاريع الإنشائية.',
                    image: '{{ asset('assets/images/news-site.jpg') }}'
                },
                {
                    slug: 'crane-rental-growth',
                    title: 'نمو خدمات تأجير الرافعات للمشاريع المتوسطة',
                    category: 'مشاريع',
                    date: '10 يونيو 2026',
                    summary: 'شركات تشغيل المعدات الثقيلة توسع خدماتها لتلبية احتياج المقاولين في المدن الرئيسية.',
                    image: '{{ asset('assets/images/news-crane.jpg') }}'
                },
                {
                    slug: 'materials-sourcing-guide',
                    title: 'دليل مختصر لاختيار موردي مواد البناء الأساسية',
                    category: 'تحديثات توريدات',
                    date: '8 يونيو 2026',
                    summary: 'إرشادات عملية تساعد الشركات على مقارنة الموردين حسب المدينة والتصنيف ونطاق الخدمة.',
                    image: '{{ asset('assets/images/news-materials.jpg') }}'
                }
            ],
            init() {
                this.applyUrlState();
                this.updateStickyNav();
                window.addEventListener('scroll', () => this.updateStickyNav(), { passive: true });
                window.addEventListener('resize', () => this.updateStickyNav());
                this.urlSyncReady = true;
                this.$watch('selectedCategory', () => {
                    this.currentPage = 1;
                    this.syncUrlState();
                });
                this.$watch('newsSearch', () => {
                    this.currentPage = 1;
                    this.syncUrlState();
                });
                this.$watch('currentPage', () => {
                    this.syncUrlState();
                });
                this.$watch('filteredNews.length', () => {
                    if (this.currentPage > this.totalPages) this.currentPage = this.totalPages;
                });
                this.syncUrlState();
            },
            updateStickyNav() {
                const nav = document.getElementById('site-sticky-nav');
                this.navStuck = window.scrollY >= ((nav?.offsetTop || 0) - 1);
            },
            get featuredNews() {
                return this.news[0];
            },
            get sideNews() {
                return this.news.slice(1, 4);
            },
            get filteredNews() {
                const search = this.newsSearch.trim();
                return this.news.filter(item => {
                    const byCategory = this.selectedCategory === 'الكل' || item.category === this.selectedCategory;
                    const bySearch = !search || `${item.title} ${item.summary} ${item.category}`.includes(search);
                    return byCategory && bySearch;
                });
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredNews.length / this.pageSize));
            },
            get pageNumbers() {
                return Array.from({ length: this.totalPages }, (_, index) => index + 1);
            },
            get paginatedNews() {
                const page = Math.min(this.currentPage, this.totalPages);
                const start = (page - 1) * this.pageSize;
                return this.filteredNews.slice(start, start + this.pageSize);
            },
            get hasActiveNewsFilter() {
                return this.selectedCategory !== 'الكل' || this.newsSearch.trim().length > 0;
            },
            get newsResultsAnnouncement() {
                const parts = [`عدد الأخبار المطابقة: ${this.filteredNews.length}`];
                const search = this.newsSearch.trim();
                if (this.selectedCategory !== 'الكل') parts.push(`ضمن ${this.selectedCategory}`);
                if (search) parts.push(`عن ${search}`);
                return parts.join(' ');
            },
            setPage(page) {
                this.currentPage = Math.min(Math.max(page, 1), this.totalPages);
            },
            nextPage() {
                this.setPage(this.currentPage + 1);
            },
            previousPage() {
                this.setPage(this.currentPage - 1);
            },
            resetNewsFilters() {
                this.selectedCategory = 'الكل';
                this.newsSearch = '';
                this.currentPage = 1;
            },
            applyUrlState() {
                const params = new URLSearchParams(location.search);
                const category = params.get('category');
                const query = this.cleanSearchQuery(params.get('q') || '');
                const page = Number.parseInt(params.get('page') || '1', 10);
                this.selectedCategory = this.categories.includes(category) ? category : 'الكل';
                this.newsSearch = query;
                this.currentPage = Number.isFinite(page) ? Math.min(Math.max(page, 1), this.totalPages) : 1;
                this.syncUrlState();
            },
            syncUrlState() {
                if (!this.urlSyncReady) return;
                const params = new URLSearchParams();
                const query = this.cleanSearchQuery(this.newsSearch);
                if (this.selectedCategory !== 'الكل') params.set('category', this.selectedCategory);
                if (query) params.set('q', query);
                if (this.currentPage > 1) params.set('page', String(Math.min(this.currentPage, this.totalPages)));
                const nextUrl = `${location.pathname}${params.toString() ? `?${params}` : ''}${location.hash}`;
                try {
                    history.replaceState(null, '', nextUrl);
                } catch (_error) {
                    // Some embedded browsers block history updates; filtering should still work.
                }
            },
            cleanSearchQuery(value) {
                return String(value)
                    .split('')
                    .filter((ch) => ch.charCodeAt(0) > 31 && ch.charCodeAt(0) !== 127)
                    .join('')
                    .trim()
                    .slice(0, 80);
            },
            imageSrcset(image) {
                const base = image.replace(/\.jpg$/, '');
                return `${base}-320.jpg 320w, ${base}-640.jpg 640w, ${image} 960w`;
            },
            countByCategory(category) {
                return category === 'الكل' ? this.news.length : this.news.filter(item => item.category === category).length;
            },
            categoryFilterLabel(category) {
                const count = this.countByCategory(category);
                const selected = this.selectedCategory === category ? 'محدد حالياً' : 'عرض';
                return `${selected}: ${category}، ${count} خبر`;
            },
            go(route) {
                if (route === 'categories') {
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
                location.href = paths[route] || '{{ route('home') }}';
            },
            newsDetailUrl(slug) {
                const from = encodeURIComponent(window.location.pathname + window.location.search);
                return newsShowBase.replace('/sample', '/' + encodeURIComponent(slug)) + '?from=' + from;
            },
            openNews(item) {
                if (!this.news.some(newsItem => newsItem.slug === item.slug)) return;
                try {
                    sessionStorage.setItem('tawreedat:lastNewsListState', location.href);
                    sessionStorage.setItem('tawreedat:selectedNews', JSON.stringify(item));
                } catch (_error) {
                    // Navigation still works if storage is unavailable.
                }
                location.href = this.newsDetailUrl(item.slug);
            },
            selectCategoryAndGo(category) {
                location.href = `{{ route('companies.index') }}?sector=${encodeURIComponent(category)}`;
            }
        }))
    })
</script>
@endpush
