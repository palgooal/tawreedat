# Frontend Pages — Tawreedat

Last updated: 2026-07-06

One row per public-facing route. "Data Source" reflects what actually feeds the page today, not what it's meant to eventually use.

| Route | Route name | Blade | Controller | Data Source | Status | Notes |
|---|---|---|---|---|---|---|
| `/` | `home` | `pages/home` | `HomeController@index` | DB (categories, companies, news, ads) + static hero/value-prop/CTA copy | Complete | Categories, companies, cities, and latest 4 published news are queried live in the controller; the 4 ad slots (`headerBanner`/`homeBanner1/2/3`) are injected separately by a view composer via `AdvertisementManager` — see `docs/DATABASE_SCHEMA.md` → "AdvertisementSlot". Empty states exist for no-categories, no-companies, no-ads. Company logos/ad images fall back to placeholder assets when missing. The hero "تصفح الشركات" button and hero search now link to a working `/companies` (see below, added 2026-07-06). |
| `/companies` | `companies.index` | `companies/index` | `CompanyController@index` | DB (`Company`, `City`, `Category`) | **Complete — MVP simple directory** (added 2026-07-06) | Active companies only, searchable (name/description/phone/email/website/city/category), filterable by city/category (slug) and verified/featured, sorted featured→verified→newest, 12/page with `withQueryString()`. Cards show contact info (phone/email/website) directly and do **not** link to `companies.show` — company profiles remain Phase 2. See `docs/DECISIONS.md`. |
| `/companies/sample` | `companies.show` | `companies/show` | none (`Route::view`) | — | **Still Deferred to Phase 2** | Placeholder route only — no Blade view exists on disk, and nothing links here anymore (the homepage's dead `openCompany()` method that used to build this URL was removed 2026-07-06). Building the real `/companies/{slug}` profile page is Phase 2 scope. |
| `/news` | `news.index` | `news/index` | `NewsController@index` | DB (`News`, `NewsCategory`) | **Production-ready** | Live search, category filter (`NewsCategory`), pagination, and a stable category sidebar (counts reflect the full published set, not the active filter). |
| `/news/{slug}` | `news.show` | `news/show` | `NewsController@show` | DB (`News`, `NewsCategory`) | **Production-ready** | Related news (same category, up to 3), category taxonomy sidebar, safe same-site "back" URL handling. Included in `/sitemap.xml`. |
| `/contact` | `contact` (GET) / `contact.store` (POST) | `pages/contact` | `ContactController@store` | DB (`ContactRequest`) | **Production-ready** (moved from "static, form not wired") | Submissions persist to `ContactRequest`, appear in Filament under طلبات التواصل, and trigger `NewContactRequestNotification` to admin-capable users. Protected by a `hp_check` honeypot field and `throttle:5,1` rate limiting on the POST route. Verified end-to-end via a live browser test on 2026-07-03 (valid submission, honeypot false-submission, rate-limit trigger). General inquiries only — company registration now has its own page (below). |
| `/register-company` | `company-registration.create` (GET) / `company-registration.store` (POST) | `company-registration/create` | `CompanyRegistrationRequestController` | DB (`CompanyRegistrationRequest`) | **Production-ready**, added 2026-07-04 | Replaces the old "سجّل شركتك" → `/plans` link. A **request/review workflow**, not a self-serve signup or paid plan: submissions persist with `status = pending`, appear in Filament under طلبات تسجيل الشركات, and trigger `NewCompanyRegistrationRequestNotification`. Same `hp_check` honeypot + `throttle:5,1` pattern as `/contact`. No online payment, no public profile activation, no company-owner account — see `docs/DECISIONS.md`. |
| `/about` | `about` | `pages/about` | none (`Route::view`) | Static | Not yet reviewed | Out of scope for the homepage-finalization pass; not audited for DB opportunities yet. |
| `/plans` | `plans` | `pages/plans` | none (`Route::view`) | Static | Not yet reviewed | Same as above. As of 2026-07-04, no "سجّل شركتك" call-to-action links here anymore (they point to `/register-company` instead) — this route/page still exists but is now effectively orphaned in the main nav; Tawreedat MVP does not use fixed pricing plans (see `docs/DECISIONS.md`). |

## Feature verification (2026-07-03)

**Contact** — Production-ready:
- ✅ Database persistence
- ✅ Notifications
- ✅ Honeypot
- ✅ Rate limiting
- ✅ Accessibility (honeypot field is `aria-hidden`, `tabindex="-1"`, unreachable by keyboard nav — invisible without breaking screen-reader flow for real fields)
- ✅ Responsive design (pre-existing Tailwind layout, unaffected by this work)

**News** — Production-ready:
- ✅ Database-backed
- ✅ News categories
- ✅ SEO integration (per-article SEO fields feeding the shared layout's meta/OG tags)
- ✅ Related news
- ✅ Sitemap inclusion

**Company Registration Request ("سجّل شركتك")** — Production-ready:
- ✅ Database persistence (`CompanyRegistrationRequest`, status `pending`)
- ✅ Notifications
- ✅ Honeypot
- ✅ Rate limiting
- ✅ Accessibility (visible labels, required markers, `old()` repopulation, `@error` messages, `role="status" aria-live="polite"` success banner, no `href="#"`)
- ✅ Responsive design (two-column desktop layout, stacks on mobile)
- **Not** a payment or subscription flow — see "Do not" list in `docs/DECISIONS.md`.

**Companies Directory** — Complete (MVP simple directory, 2026-07-06):
- ✅ Database-backed (`Company`, eager-loaded `city`/`category`)
- ✅ Search across name/description/phone/email/website/city/category
- ✅ City and category filters by slug, verified/featured filters
- ✅ Featured → verified → newest sort, 12-per-page pagination with query-string preservation
- ✅ Accessibility (visible labels on search/selects, no `href="#"`, semantic buttons/links, `alt` on logos)
- ✅ Empty state with a "مسح الفلاتر" reset
- **Not** included: company profile pages (`companies.show`), company-owner accounts, or any payment/subscription flow — see `docs/DECISIONS.md`.

**Company Profile** — remains **deferred to Phase 2** (product-scope decision, unchanged by the 2026-07-06 directory work — see `docs/DECISIONS.md`).

## Shared frontend infrastructure

- `resources/views/layouts/app.blade.php` — shared layout; `<body>` carries a single `x-data="app()"` Alpine component used across the whole site (nav, search/filter, sticky header, toast).
- `resources/views/layouts/partials/header.blade.php` / footer partials — shared header/footer, driven by the same Alpine `nav` array and `officialEntities` array (government/sector logos — intentionally static content).
- All internal links use Laravel's `route()` helper (no hardcoded paths, no `href="#"`).

## Known gaps

- `companies.show` still has a route but no real view. This is an intentional v1 scope decision (Company Profile deferred to Phase 2 — see `docs/DECISIONS.md`), not an oversight. Nothing links to it anymore as of 2026-07-06.
- About and Plans pages are static and have not yet been reviewed/finalized (`docs/ROADMAP.md` → Phase 1).
