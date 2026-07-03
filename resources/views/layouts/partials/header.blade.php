<!-- Header -->
<header class="relative z-40 border-b border-slate-200 bg-white shadow-sm">

    <!-- Top Bar -->
    <div class="bg-[linear-gradient(90deg,#012c26_0%,#014236_50%,#012c26_100%)] text-white">
        <div class="mx-auto flex h-10 max-w-[1500px] items-center justify-between px-4 text-[11px] sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold-300"></span>
                    المملكة العربية السعودية
                </span>
                <span class="hidden text-white/60 sm:inline">|</span>
                <span dir="ltr" class="hidden sm:inline">9200 123 45</span>
            </div>

            <div class="flex items-center gap-4">
                <button @click="go('contact')" class="font-bold transition hover:text-gold-300">تواصل معنا</button>
                <span class="text-white/60">|</span>
                <span class="font-bold text-gold-300">العربية</span>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="bg-white">
        <div
            class="mx-auto grid max-w-[1500px] gap-4 px-4 py-4 sm:px-6 lg:grid-cols-[280px_1fr_230px] lg:items-center lg:px-8">

            <!-- Logo -->
            <button @click="go('home')" class="flex items-center gap-4 text-right" aria-label="العودة للرئيسية">
                <span
                    class="relative grid h-[58px] w-[58px] place-items-center overflow-hidden rounded-2xl bg-gov-800 text-white shadow-xl shadow-gov-900/20">
                    <span class="absolute inset-1.5 rounded-xl border border-gold-300/50"></span>
                    <svg class="relative h-7 w-7 text-gold-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.7">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                        <path d="M3.3 7 12 12l8.7-5M12 22V12" />
                    </svg>
                </span>

                <span>
                    <strong class="block text-2xl font-extrabold text-gov-950">توريدات</strong>
                    <small class="mt-1 block text-[11px] font-medium text-slate-500">دليل مصانع مواد البناء</small>
                </span>
            </button>

            <!-- Header Ad Image -->
            <button @click="go('contact')"
                class="order-3 block overflow-hidden rounded-[28px] border border-slate-200 bg-slate-50 shadow-sm transition hover:border-gold-300 hover:shadow-md lg:order-none"
                aria-label="مساحة إعلانية">
                <img src="{{ asset('assets/images/header-ad-1-960.jpg') }}"
                    srcset="{{ asset('assets/images/header-ad-1-640.jpg') }} 640w, {{ asset('assets/images/header-ad-1-960.jpg') }} 960w"
                    sizes="(min-width: 1024px) 100vw, 100vw" width="960" height="154" alt="مساحة إعلانية في توريدات"
                    decoding="async" fetchpriority="high" class="h-[90px] w-full object-cover">
            </button>

            <!-- CTA / Mobile Menu -->
            <div class="flex items-center justify-between gap-3 lg:justify-end">
                <a href="{{ route('company-registration.create') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-gold-400 bg-[linear-gradient(90deg,#012c26,#014236)] px-5 py-3 text-sm font-bold text-white shadow-xl shadow-gov-900/15 transition hover:-translate-y-0.5 hover:shadow-2xl">
                    <span class="grid h-5 w-5 place-items-center rounded-full bg-gold-400 text-[13px] text-gov-950">+</span>
                    سجّل شركتك
                </a>

                <button class="hidden h-11 w-11 place-items-center rounded-xl border border-slate-200 lg:hidden"
                    @click="mobile=!mobile" aria-label="القائمة" aria-controls="mobile-menu" :aria-expanded="mobile.toString()">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

</header>

<!-- Navigation -->
<nav id="site-sticky-nav" class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto grid min-h-14 max-w-[1500px] grid-cols-[1fr_auto] items-center gap-4 px-4 py-2 sm:px-6 lg:min-h-16 lg:grid-cols-[auto_1fr_auto] lg:gap-6 lg:px-8">
        <a href="{{ route('home') }}"
            class="flex h-12 items-center gap-3 justify-self-start text-right transition duration-200"
            :class="navStuck ? 'translate-y-0 opacity-100' : 'pointer-events-none -translate-y-1 opacity-0'"
            :tabindex="navStuck ? null : -1"
            :aria-hidden="(!navStuck).toString()"
            aria-label="العودة للرئيسية">
            <span class="relative grid h-10 w-10 place-items-center overflow-hidden rounded-xl bg-gov-800 text-white shadow-sm shadow-gov-900/15">
                <span class="absolute inset-1 rounded-lg border border-gold-300/50"></span>
                <svg class="relative h-5 w-5 text-gold-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7">
                    <path
                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                    <path d="M3.3 7 12 12l8.7-5M12 22V12" />
                </svg>
            </span>
            <span>
                <strong class="block text-base font-extrabold leading-5 text-gov-950">توريدات</strong>
                <small class="block text-[10px] font-medium leading-4 text-slate-500">دليل مصانع مواد البناء</small>
            </span>
        </a>

        <div class="hidden items-center justify-center gap-10 lg:flex">
            <template x-for="item in nav" :key="item.id">
                <button @click="go(item.id)"
                    class="relative px-1 py-2 text-[15px] font-semibold transition after:absolute after:bottom-0 after:right-1/2 after:h-[2px] after:w-0 after:translate-x-1/2 after:rounded-full after:bg-gold-500 after:transition-all"
                    :class="route===item.id ? 'text-gold-600 after:w-8' : 'text-slate-700 hover:text-gov-900'"
                    :aria-current="route === item.id ? 'page' : null"
                    x-text="item.label"></button>
            </template>
        </div>

        <a href="{{ route('company-registration.create') }}"
            class="hidden h-10 items-center justify-center gap-2 justify-self-end rounded-xl border border-gold-400 bg-[linear-gradient(90deg,#012c26,#014236)] px-4 text-xs font-bold text-white shadow-sm shadow-gov-900/10 transition duration-200 hover:-translate-y-0.5 hover:shadow-md lg:inline-flex"
            :class="navStuck ? 'translate-y-0 opacity-100' : 'pointer-events-none -translate-y-1 opacity-0'"
            :tabindex="navStuck ? null : -1"
            :aria-hidden="(!navStuck).toString()">
            <span class="grid h-5 w-5 place-items-center rounded-full bg-gold-400 text-[13px] text-gov-950">+</span>
            سجل شركتك
        </a>

        <button class="grid h-11 w-11 place-items-center rounded-xl border border-slate-200 lg:hidden"
            @click="mobile=!mobile" aria-label="القائمة" aria-controls="mobile-menu" :aria-expanded="mobile.toString()">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu" x-show="mobile" x-cloak x-transition class="border-t bg-white px-4 py-3 lg:hidden">
        <template x-for="item in nav" :key="item.id">
            <button @click="go(item.id);mobile=false"
                class="block w-full rounded-xl px-4 py-3 text-right text-sm font-semibold"
                :class="route===item.id ? 'bg-gov-50 text-gov-800' : 'text-slate-600 hover:bg-slate-50'"
                x-text="item.label"></button>
        </template>
    </div>
</nav>
