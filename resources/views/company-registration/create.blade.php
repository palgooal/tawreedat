@php($title = 'سجّل شركتك | توريد')
@php($description = 'أرسل بيانات شركتك ليقوم فريق توريد بمراجعتها والتواصل معك لاستكمال إجراءات الظهور داخل المنصة.')
@extends('layouts.app', ['alpineComponent' => 'companyRegistrationPage'])

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
                    <span>سجّل شركتك</span>
                </div>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight sm:text-5xl">سجّل شركتك في توريد</h1>
                <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-100 sm:text-base">أرسل بيانات شركتك ليقوم فريق توريد بمراجعتها والتواصل معك لاستكمال إجراءات الظهور داخل المنصة.</p>
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-10">
                <!-- RIGHT (first in RTL DOM order): registration form -->
                <div>
                    <form method="POST" action="{{ route('company-registration.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        @csrf

                        {{-- Honeypot: real visitors never see or fill this field. Any
                             submission with it filled in is treated as spam - see
                             CompanyRegistrationRequestController@store. Same field
                             name/markup as the public contact form (hp_check, no
                             label, off-screen, autocomplete="new-password") -
                             see resources/views/pages/contact.blade.php for the
                             full history of why. --}}
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
                            <h2 class="text-2xl font-bold text-gov-950">بيانات الشركة</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">أرسل التفاصيل الأساسية وسيتولى فريق توريد مراجعة الطلب والتواصل معك.</p>
                            <p class="mt-2 text-xs font-semibold text-slate-600">الحقول المميزة بعلامة <span class="text-gold-600">*</span> مطلوبة.</p>
                        </div>

                        <div class="mt-8 grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="company-name" class="text-xs font-bold text-gov-950">اسم الشركة <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="company-name" name="company_name" value="{{ old('company_name') }}" autocomplete="organization" required
                                    aria-invalid="@error('company_name') true @else false @enderror" @error('company_name') aria-describedby="company-name-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('company_name') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="اسم الشركة أو المنشأة">
                                @error('company_name')
                                    <p id="company-name-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="contact-name" class="text-xs font-bold text-gov-950">اسم مسؤول التواصل <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="contact-name" name="contact_name" value="{{ old('contact_name') }}" autocomplete="name" required
                                    aria-invalid="@error('contact_name') true @else false @enderror" @error('contact_name') aria-describedby="contact-name-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('contact_name') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="الاسم الثلاثي">
                                @error('contact_name')
                                    <p id="contact-name-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="text-xs font-bold text-gov-950">رقم الجوال <span class="text-gold-600" aria-hidden="true">*</span></label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" dir="ltr" required
                                    aria-invalid="@error('phone') true @else false @enderror" @error('phone') aria-describedby="phone-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('phone') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="05xxxxxxxx">
                                @error('phone')
                                    <p id="phone-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="text-xs font-bold text-gov-950">البريد الإلكتروني</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" dir="ltr"
                                    aria-invalid="@error('email') true @else false @enderror" @error('email') aria-describedby="email-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('email') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="name@example.com">
                                @error('email')
                                    <p id="email-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="city_id" class="text-xs font-bold text-gov-950">المدينة</label>
                                <select id="city_id" name="city_id"
                                    aria-invalid="@error('city_id') true @else false @enderror" @error('city_id') aria-describedby="city_id-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border bg-white px-4 text-sm outline-none transition focus:border-gold-400 @error('city_id') border-red-300 @else border-slate-200 @enderror">
                                    <option value="">اختر المدينة</option>
                                    @foreach ($cities as $cityOption)
                                        <option value="{{ $cityOption->id }}" @selected((string) old('city_id') === (string) $cityOption->id)>{{ $cityOption->name }}</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <p id="city_id-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="category_id" class="text-xs font-bold text-gov-950">التصنيف / مجال العمل</label>
                                <select id="category_id" name="category_id"
                                    aria-invalid="@error('category_id') true @else false @enderror" @error('category_id') aria-describedby="category_id-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border bg-white px-4 text-sm outline-none transition focus:border-gold-400 @error('category_id') border-red-300 @else border-slate-200 @enderror">
                                    <option value="">اختر التصنيف</option>
                                    @foreach ($categories as $categoryOption)
                                        <option value="{{ $categoryOption->id }}" @selected((string) old('category_id') === (string) $categoryOption->id)>{{ $categoryOption->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p id="category_id-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="website" class="text-xs font-bold text-gov-950">الموقع الإلكتروني</label>
                                <input id="website" name="website" type="url" value="{{ old('website') }}" autocomplete="url" dir="ltr"
                                    aria-invalid="@error('website') true @else false @enderror" @error('website') aria-describedby="website-error" @enderror
                                    class="mt-2 h-12 w-full rounded-xl border px-4 text-sm outline-none transition focus:border-gold-400 @error('website') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="https://example.com">
                                @error('website')
                                    <p id="website-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="logo" class="text-xs font-bold text-gov-950">شعار الشركة</label>
                                <input id="logo" name="logo" type="file" accept="image/*"
                                    aria-invalid="@error('logo') true @else false @enderror" @error('logo') aria-describedby="logo-error" @enderror
                                    class="mt-2 block w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition file:me-3 file:rounded-lg file:border-0 file:bg-gov-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-gov-800 focus:border-gold-400 @error('logo') border-red-300 @else border-slate-200 @enderror">
                                <p class="mt-1.5 text-xs text-slate-500">اختياري، بحد أقصى 2 ميجابايت.</p>
                                @error('logo')
                                    <p id="logo-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="description" class="text-xs font-bold text-gov-950">وصف مختصر عن الشركة</label>
                                <textarea id="description" name="description"
                                    aria-invalid="@error('description') true @else false @enderror" @error('description') aria-describedby="description-error" @enderror
                                    class="mt-2 min-h-28 w-full rounded-xl border p-4 text-sm leading-7 outline-none transition focus:border-gold-400 @error('description') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="نبذة عن نشاط الشركة ومنتجاتها/خدماتها">{{ old('description') }}</textarea>
                                @error('description')
                                    <p id="description-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="notes" class="text-xs font-bold text-gov-950">ملاحظات إضافية</label>
                                <textarea id="notes" name="notes"
                                    aria-invalid="@error('notes') true @else false @enderror" @error('notes') aria-describedby="notes-error" @enderror
                                    class="mt-2 min-h-24 w-full rounded-xl border p-4 text-sm leading-7 outline-none transition focus:border-gold-400 @error('notes') border-red-300 @else border-slate-200 @enderror"
                                    placeholder="أي تفاصيل إضافية تود إخبارنا بها">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p id="notes-error" class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button type="submit" class="inline-flex h-12 items-center justify-center rounded-xl bg-gov-800 px-6 text-sm font-bold text-white transition hover:bg-gov-700">إرسال طلب التسجيل</button>
                            <p class="text-xs leading-6 text-slate-500">سيراجع الفريق طلبك ويتواصل معك خلال يوم عمل واحد.</p>
                        </div>
                    </form>
                </div>

                <!-- LEFT (second in RTL DOM order): how-it-works explanation -->
                <aside class="space-y-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-gov-950">كيف تعمل العملية؟</h2>
                        <ol class="mt-5 space-y-4 text-sm leading-7 text-slate-600">
                            <li class="flex gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gov-50 text-xs font-bold text-gov-800">١</span>
                                <span>ترسل بيانات الشركة</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gov-50 text-xs font-bold text-gov-800">٢</span>
                                <span>يراجعها فريق توريد</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gov-50 text-xs font-bold text-gov-800">٣</span>
                                <span>نتواصل معك لاستكمال التفاصيل</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gov-50 text-xs font-bold text-gov-800">٤</span>
                                <span>يتم تفعيل الظهور بعد الموافقة</span>
                            </li>
                        </ol>
                    </div>

                    <div class="rounded-2xl border border-gold-200 bg-gold-50 p-5">
                        <p class="text-sm font-semibold leading-7 text-gov-900">لا توجد مدفوعات إلكترونية حالياً. يتم الاتفاق والتحصيل عبر التواصل المباشر.</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold text-slate-500">تحتاج مساعدة؟</p>
                        <a href="tel:920012345" class="mt-2 block text-lg font-bold text-gov-950" dir="ltr">9200 123 45</a>
                        <a href="mailto:care@tawreed.sa" class="mt-1 block text-sm text-slate-600 transition hover:text-gov-800">care@tawreed.sa</a>
                    </div>
                </aside>
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

        Alpine.data('companyRegistrationPage', () => ({
            // Shared header/footer state
            route: 'company-registration',
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
