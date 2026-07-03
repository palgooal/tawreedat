# Frontend Pages — Tawreedat

Last updated: 2026-07-04

One row per public-facing route. "Data Source" reflects what actually feeds the page today, not what it's meant to eventually use.

| Route | Route name | Blade | Controller | Data Source | Status | Notes |
|---|---|---|---|---|---|---|
| `/` | `home` | `pages/home` | `HomeController@index` | DB (categories, companies, news, ads) + static hero/value-prop/CTA copy | Complete | Categories, companies, cities, latest 4 published news, and active ads (by position: `home`/`header`/`sidebar`) are all queried live. Empty states exist for no-categories, no-companies, no-ads. Company logos/ad images fall back to placeholder assets when missing. |
| `/companies` | `companies.index` | `companies/index` | none (`Route::view`) | — | **Deferred to Phase 2** | Route and route name exist, but `resources/views/companies/index.blade.php` does not exist on disk — intentional, see `docs/DECISIONS.md`. Note: the homepage's hero "تصفح الشركات" button, hero search, and category links still resolve to this route, so those entry points will error until Phase 2 ships it. |
| `/companies/sample` | `companies.show` | `companies/show` | none (`Route::view`) | — | **Deferred to Phase 2** | Same — view file intentionally not built yet for v1. `openCompany()` exists in the homepage's Alpine data (builds a `route('companies.show')` URL) but is not currently wired to any `@click` on the company cards, so nothing on the live homepage actually navigates here today. |
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

**Companies** — remains **deferred to Phase 2** (product-scope decision, not a technical gap — see `docs/DECISIONS.md`).

## Shared frontend infrastructure

- `resources/views/layouts/app.blade.php` — shared layout; `<body>` carries a single `x-data="app()"` Alpine component used across the whole site (nav, search/filter, sticky header, toast).
- `resources/views/layouts/partials/header.blade.php` / footer partials — shared header/footer, driven by the same Alpine `nav` array and `officialEntities` array (government/sector logos — intentionally static content).
- All internal links use Laravel's `route()` helper (no hardcoded paths, no `href="#"`).

## Known gaps

- `companies.index` and `companies.show` have routes but no views. This is an intentional v1 scope decision (Companies Directory/Company Profile deferred to Phase 2 — see `docs/DECISIONS.md`), not an oversight. The one loose end: the homepage's hero CTA, hero search, and category-click handlers still generate links to `companies.index`, so those specific controls will error for a user until Phase 2 lands.
- About and Plans pages are static and have not yet been reviewed/finalized (`docs/ROADMAP.md` → Phase 1).
