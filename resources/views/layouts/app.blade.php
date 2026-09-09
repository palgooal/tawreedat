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

    // Contact info and social links used by the shared header/footer
    // partials (@included below, so they share this scope). Defaults match
    // what was previously hardcoded in those partials, so nothing changes
    // visually until an admin fills these in via الإعدادات → إعدادات الموقع.
    $footerDescription = \App\Models\SiteSetting::get('site_description', 'دليل مصانع مواد البناء بالمملكة العربية السعودية.');
    $contactPhone = \App\Models\SiteSetting::get('contact_phone', '920012345');
    $contactEmail = \App\Models\SiteSetting::get('contact_email', 'info@tawreedat.sa');
    $contactAddress = \App\Models\SiteSetting::get('contact_address', 'الرياض، المملكة العربية السعودية');
    $tiktokUrl = \App\Models\SiteSetting::get('tiktok_url');
    $xUrl = \App\Models\SiteSetting::get('x_url');
    $youtubeUrl = \App\Models\SiteSetting::get('youtube_url');
    $instagramUrl = \App\Models\SiteSetting::get('instagram_url');

    // Footer "تصنيفات شائعة" — the 5 categories with the most active
    // companies (ties broken by the admin-set sort_order/name from
    // Category::scopeOrdered()), rather than a hardcoded list that can
    // drift from what actually exists in التصنيفات. Only categories with
    // at least one active company are eligible, so a footer link never
    // leads to a guaranteed-empty results page — same "no dead links"
    // convention CompanyController's own sidebar counts follow.
    $footerTopCategories = \App\Models\Category::query()
        ->where('is_active', true)
        ->withCount(['companies' => fn ($companyQuery) => $companyQuery->where('status', 'active')])
        ->ordered()
        ->get()
        ->filter(fn ($category) => $category->companies_count > 0)
        ->sortByDesc('companies_count')
        ->take(5)
        ->values();

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

    {{-- Positioned AFTER @vite(...) above (not before, as it originally was)
         so that resources/js/app.js — and anything it registers via
         Alpine.data(...), e.g. the homepage's `app` component — always
         finishes executing before this script's own auto Alpine.start()
         call fires `alpine:init`. Classic `defer` and `type="module"`
         scripts both execute in document order after parsing, so this
         script's position here (after the Vite bundle) is what guarantees
         correct registration timing, not just its `defer` attribute. --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
