{{--
    Companies directory results: the count/clear-filters strip, the
    aria-live status line, the company card grid (or empty state), and
    the pagination nav. Rendered from companies.index for a normal page
    load, and rendered STANDALONE (no layout) by CompanyController::index
    for an AJAX request (Request::ajax()) -- see resources/js/pages/companies.js,
    which fetches this same partial and swaps it into
    #companies-directory-results without a full page reload. Keeping one
    partial for both paths means the AJAX-updated markup can never drift
    from a real page load's markup.

    Expects the same variables companies.index receives from the
    controller: $companies, $activeCategoryName, $activeCityName,
    $search, $activeCitySlug, $activeCategorySlug, $verifiedOnly,
    $featuredOnly.
--}}
                <div
                    class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="leading-7 text-slate-700">
                        <span>عدد الشركات المطابقة:</span>
                        <span class="font-extrabold text-gov-950">{{ $companies->total() }}</span>
                        @if ($activeCategoryName)
                            <span>
                                <span>ضمن</span>
                                <strong class="font-bold text-gov-900">{{ $activeCategoryName }}</strong>
                            </span>
                        @endif
                        @if ($activeCityName)
                            <span>
                                <span>في</span>
                                <strong class="font-bold text-gov-900">{{ $activeCityName }}</strong>
                            </span>
                        @endif
                        @if ($search !== '')
                            <span>
                                <span>عن</span>
                                <strong class="font-bold text-gov-900">"{{ $search }}"</strong>
                            </span>
                        @endif
                    </p>
                    @if ($search !== '' || $activeCitySlug !== '' || $activeCategorySlug !== '' || $verifiedOnly || $featuredOnly)
                        <a href="{{ route('companies.index') }}"
                            class="self-start rounded-xl border border-gov-100 bg-gov-50 px-4 py-2 text-xs font-bold text-gov-900 transition hover:border-gov-200 hover:bg-gov-100 sm:self-auto">
                            عرض كل الشركات
                        </a>
                    @endif
                </div>

                <p id="companies-results-status" class="sr-only" aria-live="polite">
                    عدد الشركات المطابقة: {{ $companies->total() }}
                    @if ($activeCategoryName) ضمن {{ $activeCategoryName }} @endif
                    @if ($activeCityName) في {{ $activeCityName }} @endif
                    @if ($search !== '') عن "{{ $search }}" @endif
                </p>

                @if ($companies->isNotEmpty())
                    <div class="grid auto-rows-fr grid-cols-1 items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($companies as $company)
                            @php($logoUrl = $company->logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo) : asset('assets/images/company-placeholder.png'))
                            <article
                                class="company-result-card group relative flex min-h-[332px] flex-col rounded-3xl border border-slate-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:border-gov-200 hover:shadow-[0_12px_28px_rgba(7,30,23,0.10)]">
                                @if ($company->is_featured)
                                    <span
                                        class="absolute end-4 top-4 inline-flex items-center gap-1 rounded-full bg-gold-500 px-2.5 py-1 text-[10px] font-bold text-white shadow">
                                        مميزة
                                    </span>
                                @endif

                                @if ($company->is_verified)
                                    <span title="شركة موثقة"
                                        class="absolute start-4 top-4 inline-flex h-6 w-6 items-center justify-center rounded-full bg-gov-50 text-gov-700 shadow-sm ring-1 ring-gov-100">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.4" aria-hidden="true">
                                            <path d="M9 12.5 11 14.5 15.5 9.5" />
                                        </svg>
                                        <span class="sr-only">شركة موثقة</span>
                                    </span>
                                @endif

                                <div
                                    class="mx-auto flex h-[92px] w-[92px] items-center justify-center rounded-3xl border border-slate-200 bg-white p-4 transition duration-300 group-hover:border-gov-100">
                                    <img src="{{ $logoUrl }}" alt="{{ $company->name }}" width="92" height="92"
                                        loading="lazy" decoding="async" class="h-full w-full object-contain object-center">
                                </div>

                                <h3
                                    class="mx-auto mt-5 line-clamp-2 min-h-10 max-w-[250px] text-[18px] font-extrabold leading-7 text-gov-950">
                                    {{ $company->name }}
                                </h3>

                                <div class="mt-3 flex items-center justify-center gap-2 text-[12px] font-semibold text-slate-500">
                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <path d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>
                                    <span>{{ $company->city?->name }}</span>
                                    <span class="h-1 w-1 rounded-full bg-slate-300" aria-hidden="true"></span>
                                    <span>{{ $company->category?->name }}</span>
                                </div>

                                <p class="mx-auto mt-3 line-clamp-2 min-h-12 max-w-[280px] text-[13px] leading-6 text-slate-500">
                                    {{ $company->description }}
                                </p>

                                <div class="mt-auto pt-6">
                                    @if ($company->website)
                                        <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer"
                                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-gov-800 text-sm font-bold text-white shadow-[0_14px_28px_rgba(7,30,23,0.18)] transition hover:bg-gov-900 hover:shadow-[0_16px_32px_rgba(7,30,23,0.24)]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                aria-hidden="true">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                            </svg>
                                            <span>زيارة الموقع الإلكتروني</span>
                                        </a>
                                    @else
                                        <span aria-disabled="true"
                                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-200 text-sm font-bold text-slate-500">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                aria-hidden="true">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                                            </svg>
                                            <span>الموقع الإلكتروني غير متاح</span>
                                        </span>
                                    @endif
                                </div>
                            </article>
                        @endforeach

                        {{-- Invisible placeholder slots so the grid always occupies a
                             full PER_PAGE-sized footprint, even on a shorter last page
                             -- keeps the pagination nav below at a constant vertical
                             position across pages instead of jumping by however many
                             rows the real-card count differs by (see
                             CompanyController::index() for $missingCompanySlots).
                             Row-height equalization itself is CSS-only, via
                             auto-rows-fr on this grid: each page's rows (real
                             cards and placeholders alike) stretch to that
                             page's own tallest row, so no JS measurement is
                             needed or used here (an earlier JS-measurement
                             approach was tried and removed -- it made things
                             worse when a page's real cards have mixed natural
                             heights). 332px here mirrors this same grid's real
                             cards' own min-h-[332px] baseline just above --
                             it's only a floor for auto-rows-fr's row-height
                             calculation before/without any taller content in
                             that row. Purely presentational: aria-hidden, not
                             focusable, no pointer interaction, no content. --}}
                        @for ($i = 0; $i < $missingCompanySlots; $i++)
                            <div class="company-result-placeholder invisible pointer-events-none"
                                style="min-height: 332px" aria-hidden="true"></div>
                        @endfor
                    </div>
                @else
                    <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-14 text-center">
                        <h3 class="font-extrabold text-gov-900">لا توجد شركات مطابقة لهذه المعايير.</h3>
                        <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-600">
                            جرّب تغيير المدينة أو التصنيف أو كلمات البحث.
                        </p>
                        <a href="{{ route('companies.index') }}"
                            class="mt-5 inline-flex rounded-xl bg-gov-800 px-5 py-3 text-xs font-bold text-white">مسح الفلاتر</a>
                    </div>
                @endif

                @if ($companies->hasPages())
                    <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="ترقيم صفحات الشركات">
                        @if ($companies->onFirstPage())
                            <span class="inline-flex h-11 min-w-24 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-400 opacity-45">السابق</span>
                        @else
                            <a href="{{ $companies->previousPageUrl() }}"
                                class="inline-flex h-11 min-w-24 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">السابق</a>
                        @endif

                        @foreach ($companies->getUrlRange(1, $companies->lastPage()) as $page => $url)
                            @if ($page === $companies->currentPage())
                                <span aria-current="page"
                                    class="grid h-11 w-11 place-items-center rounded-2xl bg-gov-800 text-sm font-bold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 transition hover:border-gov-300">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($companies->hasMorePages())
                            <a href="{{ $companies->nextPageUrl() }}"
                                class="inline-flex h-11 min-w-24 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-600 transition hover:border-gov-300 hover:text-gov-900">التالي</a>
                        @else
                            <span class="inline-flex h-11 min-w-24 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-semibold text-slate-400 opacity-45">التالي</span>
                        @endif
                    </nav>
                @endif
