@php($title = 'تفاصيل الخبر | توريدات')
@php($description = 'تفاصيل خبر من أخبار منصة توريد.')
@extends('layouts.app', ['alpineComponent' => 'newsShowPage'])

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/images/hero-construction-1200.jpg') }}" fetchpriority="high">
@endpush

@section('content')

    <noscript>
        <section class="py-14">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-slate-200 bg-white p-7 sm:p-9">
                    <h1 class="text-2xl font-bold text-gov-950">يتطلب عرض تفاصيل الخبر تفعيل JavaScript</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600">يرجى تفعيل JavaScript في المتصفح لعرض محتوى الخبر والروابط ذات الصلة.</p>
                </div>
            </div>
        </section>
    </noscript>

    <!-- NEWS DETAIL -->
    <div class="fade-in" x-show="!newsNotFound" x-cloak>
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
                            <a :href="backUrl" class="inline-flex min-h-10 items-center transition hover:text-white">الأخبار</a>
                            <span class="text-white/40">/</span>
                            <span x-text="selectedNews.category"></span>
                        </nav>
                        <span class="mt-7 inline-flex rounded-full bg-gold-400 px-4 py-2 text-xs font-bold text-gov-950"
                            x-text="selectedNews.category"></span>
                        <h1 id="article-title" class="mt-5 max-w-4xl text-3xl font-extrabold leading-tight sm:text-4xl"
                            style="text-wrap: balance; overflow-wrap: anywhere;" x-text="selectedNews.title"></h1>
                        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-slate-100">
                            <time class="font-bold text-gold-200" :datetime="selectedNews.isoDate" x-text="selectedNews.date"></time>
                            <span class="hidden text-white/30 sm:inline">|</span>
                            <span>قراءة عملية لقطاع البناء والتوريد</span>
                        </div>
                        <p class="mt-5 max-w-3xl text-base leading-9 text-slate-100"
                            style="text-wrap: pretty;" x-text="selectedNews.summary"></p>
                        <a :href="backUrl"
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
                            <img :src="articleImage" :srcset="imageSrcset(articleImage)"
                                sizes="(min-width: 1024px) 920px, 100vw" :alt="articleImageAlt"
                                width="960" height="640" loading="lazy" decoding="async"
                                class="h-64 w-full object-cover sm:h-72">
                            <figcaption class="border-t border-slate-100 px-5 py-4 text-xs leading-6 text-slate-500"
                                x-text="articleImageAlt"></figcaption>
                        </figure>

                        <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9">
                            <div class="mx-auto max-w-3xl" style="max-width: 70ch; overflow-wrap: anywhere;">
                                <section aria-labelledby="intro-heading">
                                    <h2 id="intro-heading" class="text-xl font-extrabold leading-8 text-gov-950">مقدمة</h2>
                                    <p class="mt-4 text-lg leading-9 text-slate-700" style="text-wrap: pretty;" x-text="articleIntro"></p>
                                </section>

                                <section class="mt-10 border-t border-slate-100 pt-8" aria-labelledby="details-heading">
                                    <h2 id="details-heading" class="text-xl font-extrabold leading-8 text-gov-950">التفاصيل</h2>
                                    <div class="mt-4 space-y-5 text-base leading-9 text-slate-700" style="text-wrap: pretty;">
                                        <template x-for="paragraph in articleDetails" :key="paragraph">
                                            <p x-text="paragraph"></p>
                                        </template>
                                        <p x-show="!articleDetails.length" class="text-sm text-slate-500">
                                            لا تتوفر تفاصيل إضافية لهذا الخبر حالياً.
                                        </p>
                                    </div>
                                </section>

                                <section class="mt-10 border-t border-slate-100 pt-8" aria-labelledby="meaning-heading">
                                    <h2 id="meaning-heading" class="text-xl font-extrabold leading-8 text-gov-950">ماذا يعني ذلك للمقاولين والموردين؟</h2>
                                    <div class="mt-5 grid gap-3">
                                        <template x-for="point in articlePoints" :key="point">
                                            <div class="flex gap-3 rounded-2xl border border-gov-100 bg-gov-50 px-4 py-3">
                                                <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-gold-500"></span>
                                                <p class="text-sm leading-7 text-slate-700" x-text="point"></p>
                                            </div>
                                        </template>
                                        <p x-show="!articlePoints.length" class="rounded-2xl border border-gov-100 bg-gov-50 px-4 py-3 text-sm leading-7 text-slate-600">
                                            لم يتم تحديد نقاط أثر إضافية لهذا الخبر.
                                        </p>
                                    </div>
                                </section>

                                <section class="mt-10 border-t border-slate-100 pt-8" aria-labelledby="recommendations-heading">
                                    <h2 id="recommendations-heading" class="text-xl font-extrabold leading-8 text-gov-950">توصيات عملية</h2>
                                    <div class="mt-5 grid gap-3">
                                        <template x-for="takeaway in articleTakeaways" :key="takeaway">
                                            <p class="rounded-2xl border border-gold-200 bg-gold-50 px-4 py-3 text-sm font-semibold leading-7 text-gold-800"
                                                x-text="takeaway"></p>
                                        </template>
                                        <p x-show="!articleTakeaways.length" class="rounded-2xl border border-gold-200 bg-gold-50 px-4 py-3 text-sm font-semibold leading-7 text-gold-800">
                                            راجع الموردين مباشرة قبل اعتماد أي قرار شراء أو تعاقد.
                                        </p>
                                    </div>
                                </section>

                                <div class="mt-10 flex flex-col rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                                    <div>
                                        <h2 class="text-lg font-extrabold text-gov-950">هل تبحث عن موردين مرتبطين بهذا الخبر؟</h2>
                                        <p class="mt-2 text-sm leading-7 text-slate-600">
                                            انتقل إلى دليل الشركات للبحث حسب التصنيف أو المدينة ومقارنة الموردين المناسبين.
                                        </p>
                                    </div>
                                    <a :href="directoryUrl"
                                        class="mt-5 inline-flex min-h-12 items-center justify-center rounded-xl bg-gov-800 px-5 text-sm font-bold text-white transition hover:bg-gov-700">
                                        استعرض الشركات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-5 lg:sticky lg:top-24" aria-label="محتوى مرتبط">
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-base font-extrabold text-gov-950">أخبار ذات صلة</h2>
                            <div class="mt-5 space-y-4">
                                <template x-for="item in relatedNews" :key="item.slug">
                                    <a :href="newsDetailUrl(item.slug)"
                                        class="block min-h-12 w-full border-b border-slate-100 py-3 text-right last:border-0"
                                        :aria-label="`فتح خبر: ${item.title}`">
                                        <span class="text-[11px] font-bold text-gold-700" x-text="item.category"></span>
                                        <span class="mt-1 block text-sm font-bold leading-6 text-gov-950" x-text="item.title"></span>
                                        <span class="mt-1 block text-xs text-slate-500" x-text="item.date"></span>
                                    </a>
                                </template>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-base font-extrabold text-gov-950">أحدث التصنيفات</h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <template x-for="category in latestCategories" :key="category">
                                    <a :href="newsCategoryUrl(category)"
                                        class="inline-flex min-h-10 items-center rounded-full border border-slate-200 px-3 text-xs font-bold text-slate-700 transition hover:border-gold-300 hover:text-gov-900"
                                        x-text="category"></a>
                                </template>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gold-200 bg-gold-50 p-5">
                            <h2 class="text-base font-extrabold text-gov-950">تحديثات توريدات</h2>
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
    </div>

    <section class="py-14" x-show="newsNotFound" x-cloak aria-labelledby="not-found-title">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm sm:p-9">
                <p class="text-xs font-bold text-gold-700">تفاصيل الخبر</p>
                <h1 id="not-found-title" class="mt-3 text-3xl font-extrabold leading-10 text-gov-950">الخبر غير موجود</h1>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-8 text-slate-600">
                    الرابط لا يطابق أي خبر منشور حاليًا. يمكنك الرجوع إلى قائمة الأخبار بنفس حالة البحث أو التصفية السابقة.
                </p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a :href="backUrl" class="inline-flex min-h-12 items-center rounded-xl bg-gov-800 px-5 text-sm font-bold text-white">
                        العودة للأخبار
                    </a>
                    <a href="{{ route('companies.index') }}" class="inline-flex min-h-12 items-center rounded-xl border border-slate-200 px-5 text-sm font-bold text-gov-900">
                        استعراض الشركات
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div x-show="toast" x-cloak x-transition role="status" aria-live="polite" class="fixed bottom-6 right-1/2 z-[70] w-[calc(100%-2rem)] max-w-md translate-x-1/2 rounded-2xl border border-gold-300 bg-gov-950 px-5 py-4 text-center text-xs font-semibold text-white shadow-2xl" x-text="toast"></div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        const currentSlug = @js(request()->route('slug'));
        const newsShowBase = @js(route('news.show', 'sample'));
        const newsIndexUrl = @js(route('news.index'));
        const homeUrl = @js(route('home'));
        const contactUrl = @js(route('contact'));
        const companiesUrl = @js(route('companies.index'));
        const aboutUrl = @js(route('about'));
        const plansUrl = @js(route('plans'));

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
            currentSlug: currentSlug,
            selectedNews: {
                slug: '', category: '', date: '', isoDate: '', title: '', summary: '',
                image: '', imageAlt: '', body: [], points: [], takeaways: [], relatedSector: ''
            },
            newsNotFound: false,
            backUrl: newsIndexUrl,
            toast: '',
            toastTimer: null,

            news: [
                {
                    slug: 'materials-prices-update', category: 'أخبار السوق', date: '26 يونيو 2026', isoDate: '2026-06-26',
                    title: 'تحديث أسعار بعض مواد البناء هذا الأسبوع',
                    summary: 'متابعة مختصرة لأبرز التغييرات في أسعار مواد البناء لدى عدد من الموردين.',
                    image: '{{ asset('assets/images/news-1.jpg') }}',
                    imageAlt: 'مواد بناء مخزنة في موقع توريد استعداداً للشحن',
                    relatedSector: 'مواد بناء',
                    body: [
                        'يعرض هذا التحديث قراءة موجزة لحركة أسعار مواد البناء الأساسية لدى عدد من الموردين داخل المملكة.',
                        'تأتي أهمية المتابعة من ارتباط تكلفة المواد الأساسية بقرارات التسعير والشراء في المشروعات الجارية، خصوصاً عند وجود أوامر توريد ممتدة أو مشتريات مجدولة على دفعات.',
                        'تساعد القراءة الأسبوعية المقاولين على مقارنة عروض الموردين قبل تثبيت الطلبات، وتمنح الموردين فرصة أوضح لتحديث بيانات التوفر والكميات داخل قنواتهم التجارية.',
                        'تنصح توريدات بمراجعة الموردين مباشرة للتأكد من الأسعار والكميات المتاحة قبل اتخاذ قرار الشراء.'
                    ],
                    points: [
                        'مراجعة الأسعار بشكل دوري تقلل مفاجآت التكلفة عند إصدار أوامر الشراء.',
                        'المورد الذي يعرض نطاق توفر واضح يساعد فرق المشتريات على التخطيط بدقة أكبر.',
                        'توثيق عروض الأسعار وتاريخ صلاحيتها ضروري عند مقارنة أكثر من مورد.'
                    ],
                    takeaways: [
                        'اطلب عرض سعر مكتوب يتضمن مدة الصلاحية والكميات المتاحة.',
                        'قارن الموردين حسب المدينة ووقت التسليم وليس السعر فقط.',
                        'حدّث خطة الشراء إذا كان المشروع يعتمد على مواد كثيرة التقلب.'
                    ]
                },
                {
                    slug: 'ready-mix-demand', category: 'مشاريع', date: '24 يونيو 2026', isoDate: '2026-06-24',
                    title: 'طلب متزايد على موردي الخرسانة في المدن الكبرى',
                    summary: 'تشهد المشاريع السكنية والتجارية طلباً متزايداً على الخرسانة الجاهزة وخدمات الصب.',
                    image: '{{ asset('assets/images/news-2.jpg') }}',
                    imageAlt: 'شاحنة خرسانة جاهزة تعمل في موقع إنشائي',
                    relatedSector: 'خرسانة وأسمنت',
                    body: [
                        'يرتبط نمو الطلب بتوسع المشاريع السكنية والتجارية في المدن الرئيسية وحاجة المواقع إلى توريد منتظم.',
                        'تزداد أهمية اختيار موردي الخرسانة القادرين على الالتزام بالجداول الزمنية ومعايير الجودة المطلوبة في مواقع البناء، خصوصاً في مراحل الصب التي لا تحتمل تأخيراً طويلاً.',
                        'القدرة التشغيلية للمورد لا تقاس بالسعر وحده، بل تشمل قرب المحطة، مرونة الجدولة، جاهزية الأسطول، وسرعة التعامل مع تغييرات الموقع.',
                        'يوفر الدليل خيارات بحث تساعد المقاولين على الوصول إلى الموردين حسب المدينة والتصنيف.'
                    ],
                    points: [
                        'تأخير توريد الخرسانة ينعكس مباشرة على جدول التنفيذ والعمالة.',
                        'قرب المورد من الموقع عامل مؤثر في جودة الخدمة ووقت الوصول.',
                        'توثيق مواصفات الخلطة قبل التوريد يقلل الخلافات بعد الصب.'
                    ],
                    takeaways: [
                        'راجع سجل المورد في مشروعات مشابهة قبل التعاقد.',
                        'ثبت مواعيد الصب قبل وقت كاف واحتفظ بخطة بديلة.',
                        'اطلب تفاصيل الاختبارات والمواصفات الفنية ضمن عرض السعر.'
                    ]
                },
                {
                    slug: 'finishing-offers', category: 'عروض الشركات', date: '22 يونيو 2026', isoDate: '2026-06-22',
                    title: 'عروض جديدة من شركات الدهانات والتشطيبات',
                    summary: 'عدد من الموردين يطلقون عروضاً موسمية على مواد التشطيب والدهانات الداخلية والخارجية.',
                    image: '{{ asset('assets/images/news-3.jpg') }}',
                    imageAlt: 'عينات ألوان ومواد تشطيب داخلية على طاولة عمل',
                    relatedSector: 'دهانات وتشطيبات',
                    body: [
                        'تشمل العروض مواد الدهانات الداخلية والخارجية وبعض منتجات التشطيب المستخدمة في المراحل النهائية من المشاريع.',
                        'تتيح هذه العروض للمقاولين وأصحاب المشاريع مقارنة التكلفة مع الحفاظ على متطلبات الجودة والضمان، لكنها تحتاج قراءة دقيقة للشروط والكميات المشمولة.',
                        'العرض المناسب لا يعني دائماً أقل سعر؛ فقد تكون قيمة الضمان، توافر اللون، سرعة التوريد، وخدمة ما بعد البيع أكثر تأثيراً على النتيجة النهائية.',
                        'ينبغي التحقق من مدة العرض وشروط التوريد وخدمات ما بعد البيع قبل إتمام الطلب.'
                    ],
                    points: [
                        'العروض الموسمية مفيدة عند وضوح الكميات ومواعيد التنفيذ.',
                        'مقارنة الجودة والضمان أهم من الاكتفاء بسعر العبوة.',
                        'توافر اللون والدفعات المتطابقة يقلل إعادة العمل في التشطيبات.'
                    ],
                    takeaways: [
                        'اطلب عينة أو كتالوجاً معتمداً قبل اعتماد الكميات.',
                        'راجع شروط الضمان وخدمة الاستبدال.',
                        'اربط الشراء بجدول التنفيذ لتجنب تخزين طويل وغير ضروري.'
                    ]
                },
                {
                    slug: 'platform-categories-update', category: 'تحديثات توريدات', date: '20 يونيو 2026', isoDate: '2026-06-20',
                    title: 'إضافة تصنيفات جديدة داخل دليل توريدات',
                    summary: 'تحديثات جديدة تساعد الزوار على الوصول إلى الشركات حسب الخدمة والمدينة بشكل أسرع.',
                    image: '{{ asset('assets/images/news-4.jpg') }}',
                    imageAlt: 'واجهة دليل رقمي تعرض تصنيفات موردي البناء',
                    relatedSector: 'مواد بناء',
                    body: [
                        'تم تحسين تنظيم التصنيفات داخل الدليل لتسهيل الوصول إلى شركات البناء والموردين حسب نطاق الخدمة.',
                        'يساعد هذا التحديث الزوار على تقليل وقت البحث والوصول إلى نتائج أوضح عند مقارنة الشركات، خصوصاً عندما يكون الطلب مرتبطاً بخدمة محددة أو مدينة معينة.',
                        'التصنيف الدقيق يفيد الموردين أيضاً لأنه يحسن ظهورهم أمام الباحثين الجادين ويقلل الاستفسارات غير المناسبة لنطاق عملهم.',
                        'ستواصل توريدات مراجعة التصنيفات بناءً على احتياجات قطاع البناء والتوريد في المملكة.'
                    ],
                    points: [
                        'التصنيف الواضح يجعل تجربة البحث أسرع وأكثر قابلية للمقارنة.',
                        'الشركات التي تحدد خدماتها بدقة تحصل على استفسارات أكثر صلة.',
                        'تحديث بيانات النشاط والمدينة يساعد على تحسين جودة نتائج الدليل.'
                    ],
                    takeaways: [
                        'راجع تصنيف شركتك وتأكد من مطابقته للخدمة الفعلية.',
                        'أضف نطاق المدن التي تغطيها بوضوح.',
                        'استخدم كلمات وصفية عملية يفهمها المشتري والمقاول.'
                    ]
                },
                {
                    slug: 'steel-market-note', category: 'أخبار السوق', date: '18 يونيو 2026', isoDate: '2026-06-18',
                    title: 'ملاحظات سريعة حول سوق الحديد والصلب',
                    summary: 'قراءة مختصرة لاتجاهات توريد الحديد ومنتجات الصلب في مشاريع البناء.',
                    image: '{{ asset('assets/images/news-5.jpg') }}',
                    imageAlt: 'قضبان حديد وصلب مرتبة في موقع توريد',
                    relatedSector: 'حديد وصلب',
                    body: [
                        'تشير المتابعة إلى استمرار أهمية التخطيط المبكر لتوريد الحديد ومنتجات الصلب في المشاريع ذات الجداول المكثفة.',
                        'تتأثر قرارات الشراء بتوفر المقاسات والكميات ومواعيد التسليم لدى الموردين في كل مدينة، لذلك لا يكفي الاعتماد على سعر الطن وحده.',
                        'تساعد المقارنة بين الموردين على تحسين كفاءة التوريد وتقليل مخاطر التأخير، خصوصاً عند وجود مراحل إنشائية متتابعة تعتمد على وصول الحديد في وقت محدد.',
                        'ينبغي للمقاولين ربط أوامر الشراء بالمخططات المعتمدة وتحديث الكميات عند أي تعديل هندسي.'
                    ],
                    points: [
                        'توفر المقاسات قد يكون أهم من فرق بسيط في السعر.',
                        'التسليم المتدرج يحتاج اتفاقاً واضحاً مع المورد قبل بدء التنفيذ.',
                        'تحديث الكميات مبكراً يقلل الهدر والطلبات العاجلة.'
                    ],
                    takeaways: [
                        'اطلب تأكيداً مكتوباً للمقاسات والكميات المتاحة.',
                        'قارن الموردين حسب وقت التسليم والمدينة.',
                        'راجع أوامر الشراء بعد أي تعديل في المخططات.'
                    ]
                },
                {
                    slug: 'supplier-profile-tips', category: 'تحديثات توريدات', date: '15 يونيو 2026', isoDate: '2026-06-15',
                    title: 'كيف تعرض شركتك بشكل أفضل داخل الدليل؟',
                    summary: 'نصائح عملية لتحسين ظهور شركة البناء أو المورد داخل صفحات الدليل.',
                    image: '{{ asset('assets/images/news-6.jpg') }}',
                    imageAlt: 'فريق عمل يراجع ملف شركة ومعلومات مورد على شاشة',
                    relatedSector: 'مواد بناء',
                    body: [
                        'يعتمد ظهور الشركة بشكل أفضل على وضوح الوصف وتحديد التصنيفات والخدمات التي تقدمها بدقة.',
                        'إضافة معلومات التواصل ونطاق الخدمة وسابقة الأعمال تساعد الزوار على تقييم الشركة بسرعة، وتقلل الوقت الذي يحتاجه المشتري للوصول إلى قرار تواصل أولي.',
                        'الملف الجيد لا يبالغ في الوعود، بل يعرض ما تقدمه الشركة فعلياً: المدن، المنتجات، الطاقة التشغيلية، وطريقة التواصل المفضلة.',
                        'توصي توريدات بتحديث الملف دوريًا حتى يعكس قدرات الشركة الحالية ومناطق عملها.'
                    ],
                    points: [
                        'الملف الواضح يحول الزيارات إلى استفسارات أكثر جدية.',
                        'ذكر المدن والخدمات يقلل الطلبات خارج نطاق العمل.',
                        'صور الأعمال والشهادات المختصرة ترفع ثقة المشتري.'
                    ],
                    takeaways: [
                        'اكتب وصفاً عملياً لا يتجاوز احتياج المشتري.',
                        'حدّث بيانات التواصل والمدينة باستمرار.',
                        'اعرض المنتجات أو الخدمات الأهم في أول الملف.'
                    ]
                },
                {
                    slug: 'site-safety-supplies', category: 'أخبار السوق', date: '12 يونيو 2026', isoDate: '2026-06-12',
                    title: 'زيادة الطلب على مستلزمات السلامة في مواقع البناء',
                    summary: 'موردون يسجلون طلباً أعلى على معدات الوقاية وحلول السلامة مع توسع المشاريع الإنشائية.',
                    image: '{{ asset('assets/images/news-site.jpg') }}',
                    imageAlt: 'معدات سلامة وخوذ وسترات عمل داخل موقع بناء',
                    relatedSector: 'معدات سلامة',
                    body: [
                        'ترتفع الحاجة إلى مستلزمات السلامة مع توسع أعمال التنفيذ وارتفاع عدد العاملين في مواقع البناء.',
                        'تشمل المنتجات المطلوبة معدات الوقاية الشخصية ولوحات الإرشاد وحلول تنظيم الحركة داخل الموقع، وهي عناصر تؤثر على جاهزية الموقع والامتثال لمتطلبات التشغيل.',
                        'اختيار مورد موثوق يساعد على الالتزام بمتطلبات السلامة وتوفير الكميات في الوقت المناسب، خصوصاً عند بدء مراحل تنفيذ جديدة أو زيادة عدد الفرق العاملة.',
                        'ينبغي مراجعة المواصفات والاعتمادات قبل شراء أي معدات مرتبطة بسلامة العاملين.'
                    ],
                    points: [
                        'معدات السلامة ليست بنداً ثانوياً عند توسع الأعمال في الموقع.',
                        'الاعتمادات والمواصفات يجب أن تكون واضحة قبل الشراء.',
                        'توريد الكميات في الوقت المناسب يحافظ على جاهزية فرق التنفيذ.'
                    ],
                    takeaways: [
                        'راجع شهادات المنتج ومواصفاته الفنية.',
                        'احسب الكميات حسب عدد العاملين والزوار المتوقعين.',
                        'احتفظ بمورد بديل للطلبات العاجلة.'
                    ]
                },
                {
                    slug: 'crane-rental-growth', category: 'مشاريع', date: '10 يونيو 2026', isoDate: '2026-06-10',
                    title: 'نمو خدمات تأجير الرافعات للمشاريع المتوسطة',
                    summary: 'شركات تشغيل المعدات الثقيلة توسع خدماتها لتلبية احتياج المقاولين في المدن الرئيسية.',
                    image: '{{ asset('assets/images/news-crane.jpg') }}',
                    imageAlt: 'رافعة تعمل في مشروع إنشائي متوسط الحجم',
                    relatedSector: 'معدات ثقيلة',
                    body: [
                        'تشهد خدمات تأجير الرافعات طلبًا متزايدًا من المشاريع المتوسطة التي تحتاج إلى معدات لفترات محددة.',
                        'يساعد التأجير على تقليل تكلفة التملك وتوفير خيارات مرنة حسب حجم المشروع وجدوله الزمني، لكنه يتطلب وضوحاً في مدة التشغيل ومسؤوليات الصيانة والتشغيل.',
                        'توسيع خدمات الشركات المشغلة يفتح خيارات أفضل للمقاولين، خاصة عندما تتوفر المعدات مع مشغلين مؤهلين وخطط صيانة واضحة.',
                        'ينبغي التحقق من جاهزية المعدات وخطط الصيانة وخبرة المشغلين قبل التعاقد.'
                    ],
                    points: [
                        'التأجير مناسب عندما يكون احتياج المشروع مؤقتاً أو متقطعاً.',
                        'خبرة المشغلين عامل مؤثر في السلامة وسرعة الإنجاز.',
                        'وضوح بنود الصيانة والتوقف يحمي جدول المشروع.'
                    ],
                    takeaways: [
                        'حدد مدة الاستخدام والحمولات المطلوبة قبل طلب العرض.',
                        'راجع سجل الصيانة وخبرة المشغل.',
                        'اتفق على آلية التعامل مع الأعطال أو التأخير.'
                    ]
                },
                {
                    slug: 'materials-sourcing-guide', category: 'تحديثات توريدات', date: '8 يونيو 2026', isoDate: '2026-06-08',
                    title: 'دليل مختصر لاختيار موردي مواد البناء الأساسية',
                    summary: 'إرشادات عملية تساعد الشركات على مقارنة الموردين حسب المدينة والتصنيف ونطاق الخدمة.',
                    image: '{{ asset('assets/images/news-materials.jpg') }}',
                    imageAlt: 'مواد بناء أساسية مرتبة للمقارنة والاختيار',
                    relatedSector: 'مواد بناء',
                    body: [
                        'يبدأ اختيار المورد بتحديد نوع المادة والمدينة والكميات المطلوبة ومواعيد التسليم المتوقعة.',
                        'تساعد المقارنة بين أكثر من مورد على فهم الفروقات في السعر والخدمة والقدرة على التوريد، وتكشف العوامل غير الظاهرة في عرض السعر المختصر.',
                        'يوفر دليل توريدات نقطة بداية عملية للوصول إلى الموردين المناسبين والتواصل معهم مباشرة، مع إمكانية تضييق البحث حسب التصنيف ونطاق الخدمة.',
                        'الاختيار الأفضل هو الذي يوازن بين السعر، الالتزام، التوفر، وخبرة المورد في نوع المشروع.'
                    ],
                    points: [
                        'تحديد الاحتياج بدقة يختصر وقت البحث والتواصل.',
                        'المقارنة العادلة تحتاج نفس المواصفات والكميات لكل مورد.',
                        'نطاق الخدمة والمدينة يؤثران على التكلفة ووقت التسليم.'
                    ],
                    takeaways: [
                        'اكتب طلب عرض سعر موحداً قبل التواصل مع الموردين.',
                        'قارن السعر مع الالتزام ووقت التسليم.',
                        'ابدأ بالموردين الأقرب لنطاق مشروعك واحتفظ بقائمة بدائل.'
                    ]
                }
            ],

            init() {
                const params = new URLSearchParams(location.search);
                this.backUrl = this.safeReturnUrl(params.get('from')) || newsIndexUrl;
                const selected = this.news.find((item) => item.slug === this.currentSlug);
                this.newsNotFound = !selected;
                if (selected) this.selectedNews = selected;
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
            safeReturnUrl(value) {
                if (!value) return null;
                const trimmed = String(value).trim();
                if (!trimmed.startsWith('/') || trimmed.startsWith('//')) return null;
                if (/^\/[a-z][a-z0-9+.-]*:/i.test(trimmed)) return null;
                try {
                    const url = new URL(trimmed, window.location.origin);
                    if (url.origin !== window.location.origin) return null;
                    return `${url.pathname}${url.search}${url.hash}`;
                } catch (_error) {
                    return null;
                }
            },
            newsDetailUrl(slug) {
                const target = newsShowBase.replace('/sample', '/' + encodeURIComponent(slug));
                const from = this.backUrl || newsIndexUrl;
                return `${target}?from=${encodeURIComponent(from)}`;
            },
            openNews(item) {
                location.href = this.newsDetailUrl(item.slug);
            },
            showToast(message) {
                this.toast = message || 'تم تنفيذ الإجراء';
                clearTimeout(this.toastTimer);
                this.toastTimer = setTimeout(() => { this.toast = ''; }, 3200);
            },
            directoryCategoryUrl(category) {
                return category ? `${companiesUrl}?sector=${encodeURIComponent(category)}` : companiesUrl;
            },
            selectCategoryAndGo(category) {
                location.href = this.directoryCategoryUrl(category);
            },
            newsCategoryUrl(category) {
                return category ? `${newsIndexUrl}?category=${encodeURIComponent(category)}` : newsIndexUrl;
            },
            openCategory(category) {
                location.href = this.newsCategoryUrl(category);
            },
            imageSrcset(image) {
                if (!image) return '';
                const match = image.match(/^(.*)\.([a-z0-9]+)$/i);
                if (!match) return image;
                const [, base, extension] = match;
                return `${base}-320.${extension} 320w, ${base}-640.${extension} 640w, ${image} 960w`;
            },
            destroy() {
                clearTimeout(this.toastTimer);
            },
            get articleImage() {
                return this.selectedNews.image || '{{ asset('assets/images/hero-construction-1200.jpg') }}';
            },
            get articleImageAlt() {
                return this.selectedNews.imageAlt || this.selectedNews.title || 'صورة مرتبطة بالخبر';
            },
            get articleIntro() {
                const body = Array.isArray(this.selectedNews.body) ? this.selectedNews.body : [];
                return body[0] || this.selectedNews.summary || 'لا تتوفر مقدمة لهذا الخبر حالياً.';
            },
            get articleDetails() {
                return Array.isArray(this.selectedNews.body) ? this.selectedNews.body.slice(1).filter(Boolean) : [];
            },
            get articlePoints() {
                return Array.isArray(this.selectedNews.points) ? this.selectedNews.points.filter(Boolean) : [];
            },
            get articleTakeaways() {
                return Array.isArray(this.selectedNews.takeaways) ? this.selectedNews.takeaways.filter(Boolean) : [];
            },
            get directoryUrl() {
                const sector = this.selectedNews.relatedSector || this.selectedNews.category || '';
                return sector ? `${companiesUrl}?sector=${encodeURIComponent(sector)}` : companiesUrl;
            },
            get latestCategories() {
                return [...new Set(this.news.map((item) => item.category))].slice(0, 5);
            },
            get relatedNews() {
                if (this.newsNotFound) return [];
                return this.news
                    .filter((item) => item.slug !== this.selectedNews.slug && (item.category === this.selectedNews.category || item.relatedSector === this.selectedNews.relatedSector))
                    .concat(this.news.filter((item) => item.slug !== this.selectedNews.slug))
                    .filter((item, index, items) => items.findIndex((newsItem) => newsItem.slug === item.slug) === index)
                    .slice(0, 3);
            }
        }));
    });
</script>
@endpush
