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
                <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-100 sm:text-base">يسعدنا استقبال طلبات الشركات والموردين والجهات الباحثة عن موردين، وتوجيه كل طلب إلى المسار المناسب داخل منصة توريد.</p>
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
                    <form method="POST" action="{{ route('contact.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        @csrf

                        {{-- Honeypot: real visitors never see or fill this field. Any
                             submission with it filled in is treated as spam - see
                             ContactController@store. Positioned off-screen (not
                             display:none) since some bots specifically skip
                             display:none/hidden fields; aria-hidden + tabindex=-1
                             keep it out of the way for assistive tech and keyboard
                             navigation for real users. No label and a nonsense
                             field name/id (not "website") deliberately avoid
                             browser autofill heuristics - a labeled "website"
                             field was previously getting silently autofilled by
                             browsers with a saved address/company profile,
                             causing false-positive spam detection on real
                             submissions. autocomplete="new-password" is used
                             (rather than "off") because browsers largely ignore
                             autocomplete="off" for autofill purposes but do
                             respect "new-password" as a signal not to fill in a
                             stored value. --}}
                        <div class="sr-only" aria-hidden="true">
                            <input type="text" id="hp_check" name="hp_check" tabindex="-1" autocomplete="new-password">
                        </div>

                        @if (session('success'))
                            <div role="status" aria-live="polite"
                                class="mb-6 flex items-start gap-3 rounded-2xl border border-gov-200 bg-gov-50 p-4 text-sm font-semibold leading-7 text-gov-800">
                                <span class="mt-0.5 text-gold-600" aria-hidden="true">✓</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <div>
                            <h2 class="text-2xl font-bold text-gov-950">نموذج التواصل</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">أرسل التفاصيل الأساسية وسيتولى الفريق المختص مراجعة الطلب وتوجيهه.</p>
                            <p class="mt-2 text-xs font-semibold text-slate-600">الحقول المميزة بعلامة <span class="text-gold-600">*</span> مطلوبة.</p>
                        </div>

                        <div class="mt-8 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="full-name" class="text-xs font-bold text-gov-950">الاسم الكامل <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="full-name" name="name" value="{{ old('name') }}" autocomplete="name" required
                                    aria-invalid="@error('name') true @else false @enderror" @error('name') aria-describedby="name-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('name') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="الاسم الثلاثي">
                                @error('name')
                                    <p id="name-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="text-xs font-bold text-gov-950">البريد الإلكتروني <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required dir="ltr"
                                    aria-invalid="@error('email') true @else false @enderror" @error('email') aria-describedby="email-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('email') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="name@example.com">
                                @error('email')
                                    <p id="email-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="text-xs font-bold text-gov-950">رقم الجوال</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" dir="ltr"
                                    aria-invalid="@error('phone') true @else false @enderror" @error('phone') aria-describedby="phone-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('phone') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="05xxxxxxxx">
                                @error('phone')
                                    <p id="phone-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="request-type" class="text-xs font-bold text-gov-950">نوع الطلب <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <select id="request-type" name="inquiry_type" required
                                    aria-invalid="@error('inquiry_type') true @else false @enderror" @error('inquiry_type') aria-describedby="inquiry-type-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border bg-white px-4 text-sm outline-none transition focus:border-gold-400 @error('inquiry_type') border-red-300 @else border-slate-200 @enderror">
                                    <option value="" @selected(old('inquiry_type') === null)>اختر نوع الطلب</option>
                                    <option @selected(old('inquiry_type') === 'تسجيل شركة أو مورد')>تسجيل شركة أو مورد</option>
                                    <option @selected(old('inquiry_type') === 'استفسار جهة تبحث عن موردين')>استفسار جهة تبحث عن موردين</option>
                                    <option @selected(old('inquiry_type') === 'إعلانات ورعايات')>إعلانات ورعايات</option>
                                    <option @selected(old('inquiry_type') === 'شراكات')>شراكات</option>
                                    <option @selected(old('inquiry_type') === 'دعم فني')>دعم فني</option>
                                </select>
                                @error('inquiry_type')
                                    <p id="inquiry-type-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="organization" class="text-xs font-bold text-gov-950">اسم الشركة / الجهة</label>
                                <input id="organization" name="company" value="{{ old('company') }}" autocomplete="organization"
                                    aria-invalid="@error('company') true @else false @enderror" @error('company') aria-describedby="company-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('company') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="اسم المنشأة أو الجهة">
                                @error('company')
                                    <p id="company-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="message" class="text-xs font-bold text-gov-950">الرسالة <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <textarea id="message" name="message" required
                                    aria-invalid="@error('message') true @else false @enderror" @error('message') aria-describedby="message-error" @enderror
                                    class="mt-2 min-h-36 w-full rounded-xl border p-4 text-sm leading-7 outline-none transition focus:border-gold-400 @error('message') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="اكتب تفاصيل الطلب أو الاستفسار">{{ old('message') }}</textarea>
                                @error('message')
                                    <p id="message-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button type="submit" class="inline-flex h-12 items-center justify-center rounded-xl bg-gov-800 px-6 text-sm font-bold text-white transition hover:bg-gov-700">إرسال الطلب</button>
                            <p class="text-xs leading-6 text-slate-500">سيتم الرد على طلبك خلال يوم عمل واحد.</p>
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
                    <h2 class="text-2xl font-bold text-gov-950">هل ترغب في ظهور شركتك ضمن توريد؟</h2>
                    <p class="mt-2 text-sm text-slate-600">ابدأ بإنشاء حضور مهني يساعد الجهات والمقاولين على الوصول إليك بوضوح.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('company-registration.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-gov-800 px-5 text-sm font-bold text-white transition hover:bg-gov-700">سجل شركتك</a>
                    <a href="{{ route('companies.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-5 text-sm font-bold text-gov-800 transition hover:border-gold-300">استعرض الشركات</a>
                </div>
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
