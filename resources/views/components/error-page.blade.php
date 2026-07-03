@props([
    'code',
    'heading',
    'description',
])

<div class="mx-auto max-w-2xl text-center">
    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-8 shadow-sm sm:p-12">

        <div
            class="pointer-events-none absolute -left-10 -top-10 h-40 w-40 rotate-12 rounded-[36px] border border-gov-100 opacity-60">
        </div>
        <div
            class="pointer-events-none absolute -bottom-12 -right-8 h-32 w-32 rotate-45 border border-gold-200 opacity-60">
        </div>

        <div class="relative">
            <span
                class="inline-grid h-20 w-20 place-items-center rounded-3xl bg-gov-900 text-gold-300 shadow-lg shadow-gov-900/20">
                {{ $slot }}
            </span>

            <p class="mt-6 text-sm font-extrabold tracking-[0.3em] text-gold-600">{{ $code }}</p>

            <h1 class="mt-3 text-2xl font-extrabold leading-9 text-gov-950 sm:text-3xl">
                {{ $heading }}
            </h1>

            <p class="mx-auto mt-4 max-w-md text-sm leading-7 text-slate-600 sm:text-base">
                {{ $description }}
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                    class="inline-flex h-12 items-center justify-center rounded-xl bg-gov-800 px-6 text-sm font-bold text-white transition hover:bg-gov-700">
                    العودة للرئيسية
                </a>
                <a href="{{ route('contact') }}"
                    class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 px-6 text-sm font-bold text-gov-800 transition hover:border-gold-300">
                    تواصل معنا
                </a>
            </div>
        </div>
    </div>
</div>
