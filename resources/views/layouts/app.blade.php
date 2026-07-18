@php
    // Individual pages may set $seoTitle / $seoDescription / $seoImage before
    // extending this layout to override SEO on a per-page basis. The legacy
    // $title / $description variables (used by pages that predate the Site
    // Settings system) are still honoured as a fallback so no existing
    // controller had to change. Anything left unset falls back to the
    // global defaults configured in Filament (الإعدادات → إعدادات الموقع).
    $seoTitle = $seoTitle ?? $title ?? null;
    $seoDescription = $seoDescription ?? $description ?? null;
    $seoImage = $seoImage ?? null;

    $siteName = \App\Models\SiteSetting::get('site_name', 'توريد');
    $siteKeywords = \App\Models\SiteSetting::get('site_keywords');
    $defaultSeoTitle = \App\Models\SiteSetting::get('default_seo_title', $siteName);
    $defaultSeoDescription = \App\Models\SiteSetting::get(
        'default_seo_description',
        'توريد - دليل شركات البناء ومواد البناء في السعودية.'
    );
    $defaultOgImagePath = \App\Models\SiteSetting::get('default_og_image');
    $googleSiteVerification = \App\Models\SiteSetting::get('google_search_console_verification');
    $googleAnalyticsId = \App\Models\SiteSetting::get('google_analytics_id');

    $resolvedSeoTitle = $seoTitle ?: $defaultSeoTitle;
    $resolvedSeoDescription = $seoDescription ?: $defaultSeoDescription;
    $resolvedSeoImage = $seoImage
        ?: ($defaultOgImagePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($defaultOgImagePath) : null);
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $resolvedSeoTitle }}</title>
    <meta name="description" content="{{ $resolvedSeoDescription }}">
    @if ($siteKeywords)
        <meta name="keywords" content="{{ $siteKeywords }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $resolvedSeoTitle }}">
    <meta property="og:description" content="{{ $resolvedSeoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if ($resolvedSeoImage)
        <meta property="og:image" content="{{ $resolvedSeoImage }}">
    @endif

    <meta name="twitter:card" content="{{ $resolvedSeoImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $resolvedSeoTitle }}">
    <meta name="twitter:description" content="{{ $resolvedSeoDescription }}">
    @if ($resolvedSeoImage)
        <meta name="twitter:image" content="{{ $resolvedSeoImage }}">
    @endif

    @if ($googleSiteVerification)
        <meta name="google-site-verification" content="{{ $googleSiteVerification }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    @if ($googleAnalyticsId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalyticsId }}');
        </script>
    @endif
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{{ $alpineComponent ?? 'app' }}()" @notify.window="showToast($event.detail)">

    @include('layouts.partials.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')
</body>
</html>
