<!-- Footer -->
<footer class="bg-gov-950 bg-[linear-gradient(135deg,#012c26,#014236,#012c26)] text-white">
    <div
        class="mx-auto grid max-w-[1500px] gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-8">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-right" aria-label="العودة للرئيسية">
                <span
                    class="soft-float grid h-12 w-12 place-items-center rounded-2xl border border-gold-300/40 text-gold-300">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                        <path d="M3.3 7 12 12l8.7-5M12 22V12" />
                    </svg>
                </span>
                <span>
                    <strong class="block text-2xl font-extrabold">توريدات</strong>
                    <small class="mt-1 block text-[10px] text-slate-400">دليل مصانع مواد البناء</small>
                </span>
            </a>
            <p class="mt-5 max-w-sm text-sm leading-7 text-slate-400">
                دليل مصانع مواد البناء بالمملكة العربية السعودية.
            </p>
            <div class="mt-6 flex gap-2">
                <span aria-disabled="true" aria-label="حساب منصة إكس غير متاح حالياً"
                    class="grid h-9 w-9 place-items-center rounded-xl border border-white/10 text-xs text-slate-400">X</span>
                <span aria-disabled="true" aria-label="حساب لينكدإن غير متاح حالياً"
                    class="grid h-9 w-9 place-items-center rounded-xl border border-white/10 text-xs text-slate-400">in</span>
                <span aria-disabled="true" aria-label="حساب إنستغرام غير متاح حالياً"
                    class="grid h-9 w-9 place-items-center rounded-xl border border-white/10 text-xs text-slate-400">IG</span>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-gold-300">روابط سريعة</h3>
            <div class="mt-5 space-y-3 text-sm text-slate-400">
                <a href="{{ route('home') }}" class="block transition hover:text-white">الرئيسية</a>
                <a href="{{ route('companies.index') }}" class="block transition hover:text-white">الشركات</a>
                <a href="{{ route('home') }}#companies" class="block transition hover:text-white">التصنيفات</a>
                <a href="{{ route('news.index') }}" class="block transition hover:text-white">الأخبار</a>
                <a href="{{ route('about') }}" class="block transition hover:text-white">من نحن</a>
                <a href="{{ route('contact') }}" class="block transition hover:text-white">تواصل معنا</a>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-gold-300">تصنيفات شائعة</h3>
            <div class="mt-5 space-y-3 text-sm text-slate-400">
                <button @click="selectCategoryAndGo('مواد بناء')" aria-label="عرض شركات تصنيف مواد بناء"
                    class="block transition hover:text-white">مواد بناء</button>
                <button @click="selectCategoryAndGo('خرسانة وأسمنت')" aria-label="عرض شركات تصنيف خرسانة وأسمنت"
                    class="block transition hover:text-white">خرسانة
                    وأسمنت</button>
                <button @click="selectCategoryAndGo('حديد وصلب')" aria-label="عرض شركات تصنيف حديد وصلب"
                    class="block transition hover:text-white">حديد وصلب</button>
                <button @click="selectCategoryAndGo('كهرباء وإنارة')" aria-label="عرض شركات تصنيف كهرباء وإنارة"
                    class="block transition hover:text-white">كهرباء
                    وإنارة</button>
                <button @click="selectCategoryAndGo('دهانات وتشطيبات')" aria-label="عرض شركات تصنيف دهانات وتشطيبات"
                    class="block transition hover:text-white">دهانات
                    وتشطيبات</button>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-gold-300">تواصل معنا</h3>
            <div class="mt-5 space-y-3 text-sm text-slate-400">
                <p>الرياض، المملكة العربية السعودية</p>
                <a href="tel:920012345" dir="ltr" class="block text-right transition hover:text-white">9200 123 45</a>
                <a href="mailto:info@tawreedat.sa" class="block transition hover:text-white">info@tawreedat.sa</a>
                <a href="{{ route('contact') }}" class="block transition hover:text-white">تواصل معنا</a>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div
            class="mx-auto flex max-w-[1500px] flex-col justify-between gap-3 px-4 py-6 text-xs text-slate-400 sm:flex-row sm:px-6 lg:px-8">
            <p>© 2026 توريدات. جميع الحقوق محفوظة.</p>
            <div class="flex gap-5">
                <span aria-disabled="true">سياسة الخصوصية</span>
                <span aria-disabled="true">الشروط والأحكام</span>
            </div>
        </div>
    </div>
</footer>
