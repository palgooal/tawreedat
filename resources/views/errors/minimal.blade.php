<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'حدث خطأ | توريد' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-[1500px] items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-right" aria-label="العودة للرئيسية">
                <span
                    class="relative grid h-11 w-11 place-items-center overflow-hidden rounded-2xl bg-gov-800 text-white shadow-sm shadow-gov-900/15">
                    <span class="absolute inset-1 rounded-xl border border-gold-300/50"></span>
                    <svg class="relative h-5 w-5 text-gold-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.7">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                        <path d="M3.3 7 12 12l8.7-5M12 22V12" />
                    </svg>
                </span>
                <span>
                    <strong class="block text-lg font-extrabold leading-5 text-gov-950">توريد</strong>
                    <small class="block text-[10px] font-medium text-slate-500">دليل مصانع مواد البناء</small>
                </span>
            </a>
            <a href="{{ route('contact') }}"
                class="hidden rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-gov-800 transition hover:border-gold-300 sm:inline-flex">
                تواصل معنا
            </a>
        </div>
    </header>

    <main class="flex flex-1 items-center py-14 sm:py-20">
        <div class="mx-auto w-full max-w-[1500px] px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white py-6">
        <div
            class="mx-auto flex max-w-[1500px] flex-col items-center justify-between gap-3 px-4 text-center text-xs text-slate-500 sm:flex-row sm:px-6 sm:text-right lg:px-8">
            <p>© {{ date('Y') }} توريد. جميع الحقوق محفوظة.</p>
            <div class="flex gap-5">
                <a href="{{ route('home') }}" class="transition hover:text-gov-800">الرئيسية</a>
                <a href="{{ route('news.index') }}" class="transition hover:text-gov-800">الأخبار</a>
                <a href="{{ route('contact') }}" class="transition hover:text-gov-800">تواصل معنا</a>
            </div>
        </div>
    </footer>

</body>
</html>
