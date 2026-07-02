@php($title = 'تواصل معنا | توريد')
@php($description = 'تواصل مع فريق توريد للاستفسارات والدعم وطلبات انضمام الموردين والشركات.')
@extends('layouts.app', ['alpineComponent' => 'contactPage'])

@section('content')

    <!-- Page Hero -->
    <section class="relative flex min-h-[320px] items-center overflow-hidden bg-gov-950 py-14 text-white sm:py-16">
        <img src="{{ asset('assets/images/hero-construction-1200.jpg') }}"
            srcset="{{ asset('assets/images/hero-construction-768.jpg') }} 768w, {{ asset('assets/images/hero-construction-1200.jpg') }} 1200w, {{ asset('assets/images/hero-construction.jpg') }} 1600w"
            sizes="100vw" alt="" width="1600" height="900" decoding="async" fetchpriority="high"
            class="absolute inset-0 h-full w-full object-cover opacity-25">
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,50,42,.95),rgba(0,58,48,.88),rgba(0,50,42,.95))]"></div>
        <div class="relative mx-auto w-full max-w-[1500px] px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="flex items-center gap-2 text-xs font-bold text-gold-200">
                    <a href="{{ route('home') }}" class="transition hover:text-white">الرئيسية</a>
                    <span class="text-white/40">/</span>
                    <span>تواصل معنا</span>
                </div>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight sm:text-5xl">تواصل معنا</h1>
                <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-100 sm:text-base">يسعدنا استقبال طلبات الشركات والموردين والجهات الباحثة عن موردين، وتوجيه كل طلب إلى المسار المناسب داخل منصة توريدات.</p>
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap gap-6 text-sm font-semibold text-gov-900">
                <span class="inline-flex items-center gap-2"><span class="text-gold-600" aria-hidden="true">✓</span>رد خلال يوم عمل</span>
                <span class="inline-flex items-center gap-2"><span class="text-gold-600" aria-hidden="true">✓</span>دعم للشركات والموردين</span>
                <span class="inline-flex items-center gap-2"><span class="text-gold-600" aria-hidden="true">✓</span>قنوات تواصل مباشرة</span>
            </div>
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_400px] lg:gap-10">
                <div>
                    <form @submit.prevent="submitContactForm()" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        <div>
                            <h2 class="text-2xl font-bold text-gov-950">نموذج التواصل</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">أرسل التفاصيل الأساسية وسيتولى الفريق المختص مراجعة الطلب وتوجيهه.</p>
                            <p class="mt-2 text-xs font-semibold text-slate-600">الحقول المميزة بعلامة <span class="text-gold-600">*</span> مطلوبة.</p>
                        </div>

                        <div class="mt-8 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="full-name" class="text-xs font-bold text-gov-950">الاسم الكامل <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="full-name" name="full_name" autocomplete="name" required class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-gold-400" placeholder="الاسم الثلاثي">
                            </div>
                            <div>
                                <label for="email" class="text-xs font-bold text-gov-950">البريد الإلكتروني <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="email" name="email" type="email" autocomplete="email" required dir="ltr" class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-gold-400" placeholder="name@example.com">
                            </div>
                            <div>
                                <label for="phone" class="text-xs font-bold text-gov-950">رقم الجوال</label>
                                <input id="phone" name="phone" type="tel" autocomplete="tel" dir="ltr" class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-gold-400" placeholder="05xxxxxxxx">
                            </div>
                            <div>
                                <label for="request-type" class="text-xs font-bold text-gov-950">نوع الطلب <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <select id="request-type" name="request_type" required class="mt-2 h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm outline-none transition focus:border-gold-400">
                                    <option value="">اختر نوع الطلب</option>
                                    <option>تسجيل شركة أو مورد</option>
                                    <option>استفسار جهة تبحث عن موردين</option>
                                    <option>إعلانات ورعايات</option>
                                    <option>شراكات</option>
                                    <option>دعم فني</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="organization" class="text-xs font-bold text-gov-950">اسم الشركة / الجهة</label>
                                <input id="organization" name="organization" autocomplete="organization" class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-gold-400" placeholder="اسم المنشأة أو الجهة">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="message" class="text-xs font-bold text-gov-950">الرسالة <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <textarea id="message" name="message" required class="mt-2 min-h-36 w-full rounded-xl border border-slate-200 p-4 text-sm leading-7 outline-none transition focus:border-gold-400" placeholder="اكتب تفاصيل الطلب أو الاستفسار"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button type="submit" class="inline-flex h-12 items-center justify-center rounded-xl bg-gov-800 px-6 text-sm font-bold text-white transition hover:bg-gov-700">إرسال الطلب</button>
                            <p class="text-xs leading-6 text-slate-500">هذا النموذج تجريبي حالياً وسيتم ربطه بالنظام لاحقاً.</p>
                        </div>
                    </form>
                </div>

                <aside class="space-y-5">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                        <a href="tel:920012345" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-gold-300">
                            <p class="text-xs font-bold text-slate-500">الهاتف</p>
                            <b class="mt-2 block text-lg text-gov-950" dir="ltr">9200 123 45</b>
                        </a>
                        <a href="mailto:care@tawreed.sa" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-gold-300">
                            <p class="text-xs font-bold text-slate-500">البريد الإلكتروني</p>
                            <b class="mt-2 block text-base text-gov-950">care@tawreed.sa</b>
                        </a>
                        <a href="{{ route('contact') }}" aria-current="page" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-gold-300">
                            <p class="text-xs font-bold text-slate-500">تواصل معنا</p>
                            <b class="mt-2 block text-base text-gov-950">نموذج الطلبات والاستفسارات</b>
                        </a>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-bold text-slate-500">ساعات العمل</p>
                            <b class="mt-2 block text-base text-gov-950">الأحد - الخميس، 8:00 صباحاً - 5:00 مساءً</b>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="pb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div>
                    <h2 class="text-2xl font-bold text-gov-950">هل ترغب في ظهور شركتك ضمن توريدات؟</h2>
                    <p class="mt-2 text-sm text-slate-600">ابدأ بإنشاء حضور مهني يساعد الجهات والمقاولين على الوصول إليك بوضوح.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('plans') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-gov-800 px-5 text-sm font-bold text-white transition hover:bg-gov-700">سجل شركتك</a>
                    <a href="{{ route('companies.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-5 text-sm font-bold text-gov-800 transition hover:border-gold-300">استعرض الشركات</a>
                </div>
            </div>
        </div>
    </section>

    <div x-show="toast" x-cloak x-transition role="status" aria-live="polite" class="fixed bottom-6 right-1/2 z-[70] w-[calc(100%-2rem)] max-w-md translate-x-1/2 rounded-2xl border border-gold-300 bg-gov-950 px-5 py-4 text-center text-xs font-semibold text-white shadow-sm">
        تم استلام طلبك، وسيتم ربط النموذج بالنظام لاحقاً
    </div>

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

        Alpine.data('contactPage', () => ({
            // Shared header/footer state
            route: 'contact',
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
            toast: false,

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
            },
            submitContactForm() {
                this.toast = true;
                setTimeout(() => this.toast = false, 2600);
            }
        }));
    });
</script>
@endpush
