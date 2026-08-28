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
                    <strong class="block text-2xl font-extrabold">توريد</strong>
                    <small class="mt-1 block text-[10px] text-slate-400">دليل مصانع مواد البناء</small>
                </span>
            </a>
            <p class="mt-5 max-w-sm text-sm leading-7 text-slate-400">
                {{ $footerDescription }}
            </p>
            <div class="mt-6 flex gap-2">
                @php
                    // Real brand marks (Simple Icons paths, single <path> each,
                    // viewBox 0 0 24 24) instead of plain "F"/"X"/"in"/"IG" text
                    // labels — those read as unfinished placeholder UI rather
                    // than recognizable platform icons.
                    $socialLinks = [
                        [
                            'url' => $facebookUrl,
                            'name' => 'فيسبوك',
                            'path' => 'M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z',
                        ],
                        [
                            'url' => $xUrl,
                            'name' => 'منصة إكس',
                            'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
                        ],
                        [
                            'url' => $linkedinUrl,
                            'name' => 'لينكدإن',
                            'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
                        ],
                        [
                            'url' => $instagramUrl,
                            'name' => 'إنستغرام',
                            'path' => 'M12 0C8.74 0 8.333.014 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.014 8.333 0 8.74 0 12s.014 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.014 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.014-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.014 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.795.646-1.439 1.44-1.439.793-.001 1.44.644 1.44 1.439z',
                        ],
                    ];
                @endphp
                @foreach ($socialLinks as $social)
                    @if ($social['url'])
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                            aria-label="حساب توريد على {{ $social['name'] }}"
                            class="grid h-9 w-9 place-items-center rounded-xl border border-white/10 text-slate-400 transition hover:border-gold-300/40 hover:text-gold-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                                <path d="{{ $social['path'] }}" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="حساب {{ $social['name'] }} غير متاح حالياً"
                            class="grid h-9 w-9 place-items-center rounded-xl border border-white/10 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                                <path d="{{ $social['path'] }}" />
                            </svg>
                        </span>
                    @endif
                @endforeach
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
                @forelse ($footerTopCategories as $footerCategory)
                    <a href="{{ route('companies.index') }}?category={{ $footerCategory->slug }}"
                        aria-label="عرض شركات تصنيف {{ $footerCategory->name }}"
                        class="block transition hover:text-white">{{ $footerCategory->name }}</a>
                @empty
                    <a href="{{ route('companies.index') }}" class="block transition hover:text-white">تصفح جميع الشركات</a>
                @endforelse
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-gold-300">تواصل معنا</h3>
            <div class="mt-5 space-y-3 text-sm text-slate-400">
                @if ($contactAddress)
                    <p>{{ $contactAddress }}</p>
                @endif
                @if ($contactPhone)
                    <a href="tel:{{ $contactPhone }}" dir="ltr" class="block text-right transition hover:text-white">{{ $contactPhone }}</a>
                @endif
                @if ($contactEmail)
                    <a href="mailto:{{ $contactEmail }}" class="block transition hover:text-white">{{ $contactEmail }}</a>
                @endif
                <a href="{{ route('contact') }}" class="block transition hover:text-white">تواصل معنا</a>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div
            class="mx-auto flex max-w-[1500px] flex-col justify-between gap-3 px-4 py-6 text-xs text-slate-400 sm:flex-row sm:px-6 lg:px-8">
            <p>© 2026 توريد. جميع الحقوق محفوظة.</p>
            <div class="flex gap-5">
                <a href="{{ route('pages.show', 'privacy') }}" class="transition hover:text-white">سياسة الخصوصية</a>
                <a href="{{ route('pages.show', 'terms') }}" class="transition hover:text-white">الشروط والأحكام</a>
            </div>
        </div>
    </div>
</footer>
