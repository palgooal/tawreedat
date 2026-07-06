# Architectural Decisions — Tawreedat

Last updated: 2026-07-06

Chronological log of non-obvious technical decisions and why they were made. When a future change contradicts one of these, update the entry rather than deleting it — the "why" is often more valuable than the current state.

---

## 2026-07-03

- **Use Laravel 12 + Filament for the admin panel**, rather than a hand-rolled admin area. Filament gives resource CRUD, filters, and RBAC-ready scaffolding for free.
- **Adopted Filament v5 instead of pinning to v3.** The initial intent was v3 (a caret-quoting bug in the PowerShell `composer require` command accidentally pinned an exact v3.2.0 version and caused a resolution conflict). Once the version constraint was corrected, Composer resolved to v5.6.8, which is what's actually installed. All resources, the panel provider, and widgets were written against the v5 API (notably: `Schemas` namespace restructuring, `UnitEnum|string|null` typed `$navigationGroup`, no `->rtl()` panel method). Decision: stay on v5 rather than force a v3 downgrade, since v5 was what successfully resolved and installed.
- **RTL is locale-driven, not an explicit API call.** Filament v5's `Panel` class has no `->rtl()` method (verified against vendor source). Direction follows `APP_LOCALE=ar` automatically. The public site sets `direction: rtl` globally in `resources/css/app.css`. There is intentionally no LTR variant.
- **Use Tailwind CSS v4** with the CSS-first `@theme`/`@source` config (no `tailwind.config.js`). Brand colors (Gov green, Gold) are declared as `@theme` tokens so they're usable both as utility classes and as CSS variables.
- **Use Arabic-safe slugs** (`Str::slug($value, '-', null)`) instead of the Laravel default. `Str::slug()`'s default ASCII-transliteration step silently strips Arabic characters, producing empty slugs for Arabic-named records — this would have broken `firstOrCreate`-by-slug uniqueness in the demo seeder. Fixed at the model level (`City`, `Category`, `Company`, `News`, all via a `static::saving()` hook that only fills the slug if blank) and mirrored in the matching Filament resource form fields (live slug preview on create).
- **Shared header/footer through Blade partials**, driven by a single Alpine `x-data="app()"` component declared on `<body>` in `layouts/app.blade.php`. All pages that extend this layout share the same `nav`, `officialEntities`, and UI-state logic — there is deliberately no per-page Alpine component.
- **Sticky nav only, not a sticky full header.** A single `<nav id="site-sticky-nav" class="sticky ...">` element is sticky; the rest of the header scrolls away normally. A compact brand mark and CTA fade in/out inside that sticky nav based on scroll position (`navStuck`), rather than duplicating a second fixed header bar.
- **Homepage data is fetched server-side** (`HomeController@index`), not client-side via API/fetch calls. Categories, companies, cities, latest news, and active ads are all queried in the controller and passed to the Blade view as `@json(...)`-encoded arrays consumed by Alpine for client-side filtering/search only. Alpine never talks to the backend directly on this page.
- **File uploads (Company logo, News image, Advertisement image) are explicitly pinned to the `public` disk** (`->disk('public')` on each `FileUpload` field). Filament's default filesystem disk is `local` (private) unless configured otherwise; without this, uploaded images would never resolve to a public URL. `php artisan storage:link` is required once per environment.
- **Custom Filament theme built via `->viteTheme()` + a dedicated CSS file only** — no vendor files edited, no extra UI packages installed, no non-functional buttons added to the admin shell. Branding (topbar, dashboard welcome section, stat widgets) uses only supported Panel APIs: render hooks (`PanelsRenderHook::USER_MENU_BEFORE`, `PanelsRenderHook::GLOBAL_SEARCH_BEFORE`), a `brandName()` closure view, and theme CSS.
- **Demo/seed data intentionally deviates from originally-requested literal values where they didn't match the actual schema.** For example, requested advertisement "type" values (`header_banner`/`homepage_banner`/`sidebar_banner`) were mapped to the schema's actual `position` enum (`header`/`home`/`sidebar`), and a requested contact-request status of `in_review` was mapped to the schema's actual `in_progress`. The seeder (`TawreedatDemoSeeder`) is idempotent via `firstOrCreate`/`updateOrCreate` keyed on natural-unique fields (slug, email+inquiry_type, title), so it's safe to re-run.
- **Homepage counters wired but not yet surfaced in the UI.** `HomeController` now computes `categoriesCount`, `companiesCount`, `citiesCount`, and `verifiedCompaniesCount` and passes them to the view, but the approved homepage design has no dedicated stats-bar element to place them in (the sidebar already shows live counts via `categories.length`/`companies.length` client-side). Decision: leave the counts available in the view rather than force them into the existing design; revisit if a stats section is explicitly requested.
- **Admin access control uses `spatie/laravel-permission` with 4 fixed roles and 13 fixed permissions, not a custom role builder.** A production-readiness audit flagged that every authenticated user previously had full admin access (no `canAccessPanel()` gate at all). The fix was staged in two steps: first an `is_admin` boolean as a minimal stopgap (any user, one flag, in-or-out of the whole panel), then this roles/permissions system once resource-level granularity was actually needed (Editor should only touch content, Support should only touch contact requests, etc.). Deliberately did **not** reach for a heavier package (e.g. `filament-shield`) or build a role-management UI — 4 roles and 13 permissions are hardcoded in `RolesAndPermissionsSeeder` rather than editable through Filament, because the brief was explicit about keeping this simple for MVP and not building full RBAC. If more than these 4 roles are ever needed, that's the point to reconsider this decision, not before.
- **`is_admin` is transitional, not removed.** Once roles existed, `is_admin` could have been deleted outright. It wasn't, because doing so would silently lock out any account created between the `is_admin` migration and the roles/permissions migration landing, with no way to know in advance whether such an account exists. `canAccessPanel()` checks `is_admin OR hasAnyRole([...])` so both eras of account remain valid. `is_admin` is checked **only** for panel entry - it is never read by any Resource/Page's `canViewAny()`/`canCreate()`/`canEdit()`/`canDelete()`/`canAccess()`, all of which go through permissions (or, for `UserResource` specifically, the Super Admin role directly). Tracked in `docs/ROADMAP.md` as a Phase 2 cleanup: remove `is_admin` once every real admin account is confirmed to have a role.
- **`UserResource` checks the Super Admin role directly instead of a `manage users` permission, even though that permission exists.** User/role management is the one resource in this panel where getting authorization wrong has the worst consequence (privilege escalation), so it was deliberately kept out of the generic, seeder-editable permission grid and hardcoded instead. The `manage users`/`view users` permissions still exist and are excluded from the `Admin` role's grant (see `RolesAndPermissionsSeeder`) so the permission model documents the intent even though `UserResource` doesn't actually consult it.
- **Companies Directory (`/companies`) and Company Profile (`/companies/{slug}`) are deferred to Phase 2 — not part of v1 scope.** v1 of the Tawreedat platform is deliberately scoped to: introducing the platform, news and advertisements, attracting organic traffic, building brand identity/trust, and capturing contact/join requests — not full company-profile browsing. This is a product decision, not a technical blocker (the routes already exist; only the views/controllers are missing). Consequence: the homepage's hero "تصفح الشركات" button, hero search, and category-click links still resolve to `companies.index`, so those specific controls will error until Phase 2 ships — accepted as a known v1 loose end rather than reason to build the directory early. See `docs/ROADMAP.md` for the updated phase breakdown (News and Contact-form wiring were promoted into Phase 1 as a result, since both are explicitly part of the stated v1 focus). **Superseded in part 2026-07-06:** the Companies Directory half of this decision was reversed — see the 2026-07-06 entries below. Company Profile remains deferred exactly as decided here.

**Decision:** Use `spatie/laravel-permission` instead of a custom RBAC implementation.
**Reason:**
  - Proven ecosystem — widely used, well-maintained, actively developed.
  - Filament compatibility — integrates cleanly with Filament's `FilamentUser`/authorization hooks without a bridging package.
  - Future scalability — supports adding roles/permissions later without a schema rewrite, if the 4-role/13-permission MVP scope ever needs to grow.
  (Full rationale, including why a heavier package like `filament-shield` was deliberately not used: see the roles/permissions entry above, dated 2026-07-03.)

**Decision:** Keep `is_admin` temporarily rather than removing it once roles/permissions landed.
**Reason:**
  - Safe migration path — removing it outright risked silently locking out any account created between the `is_admin` migration and the roles/permissions migration, with no way to know in advance if one exists.
  - Backward compatibility — `canAccessPanel()` accepts either `is_admin = true` or a panel role, so both eras of account remain valid without manual intervention.
  - Easier deployment — no forced "assign a role to every existing user before deploying this change" step. Tracked in `docs/ROADMAP.md` → Future, for removal once every real admin account is confirmed to have a role.

**Decision:** Use a honeypot field + rate limiting for the public contact form instead of a CAPTCHA.
**Reason:**
  - Better UX — no visual/interactive challenge for real visitors to solve.
  - Lower friction — nothing for a legitimate submitter to get wrong or be blocked by (no third-party widget to load, no accessibility concerns from an image/audio challenge).
  - Sufficient for MVP — a hidden `hp_check` field (invisible to real users, silently caught by naive bots) combined with `throttle:5,1` on `POST /contact` blocks both classes of low-effort abuse this stage of the product needs to worry about. Revisit if spam volume in production data shows this isn't enough. Verified end-to-end via a live browser test on 2026-07-03 — see `docs/PROJECT_STATUS.md` → "Contact Form & Spam Protection".

**Decision:** Dashboard widget visibility is driven by the existing `Permissions` constants (`app/Support/Permissions.php`), not by hardcoded role-name checks.
**Reason:**
  - Single source of truth — the same permission that already gates a Resource (e.g. `manage news`) also gates the dashboard card that summarizes it, so the two can never silently drift apart the way two independently-maintained role lists could.
  - Automatically correct for future roles — if a 5th role is ever added in `RolesAndPermissionsSeeder` with some subset of these permissions, the dashboard adapts without any widget code changing.
  - The one exception is `QuickActionsWidget`'s "إدارة المستخدمين" button and `KpiOverviewWidget`/`AnalyticsWidget`'s admin-user counts, which check `hasRole('Super Admin')` directly rather than a permission — this deliberately mirrors `UserResource`'s own Super-Admin-only gate (see the "UserResource checks the Super Admin role directly" entry above), so the dashboard never shows a button or number tied to a resource a user can't actually reach.

**Decision:** Remove the old ad-hoc dashboard widgets (`WelcomeWidget`, `TawreedatStatsWidget`, `RecentCompaniesWidget`) rather than keep them alongside the new executive-dashboard widget set.
**Reason:**
  - Filament's `discoverWidgets()` auto-registers every widget class found in `app/Filament/Widgets`, independent of the explicit `->widgets([...])` list in `AdminPanelProvider` — leaving the old files in place would have kept showing them regardless of the new curated set.
  - Several of the old widgets' numbers (company/news/contact-request counts) would have duplicated the new `KpiOverviewWidget`/`AnalyticsWidget` cards, working directly against the brief's "information density over decorative elements" principle and cluttering what's meant to read as a deliberate, curated control center rather than an accumulation of ad-hoc stat cards.
  - `RecentCompaniesWidget` in particular fell outside the new dashboard's scope entirely (Companies isn't one of the 6 specified widget areas) and duplicated what `CompanyResource`'s own list page already shows.

## 2026-07-04

**Decision:** "سجّل شركتك" is a manually-reviewed **registration request**, not a self-serve signup, subscription plan, or online payment flow.
**Reason:**
  - Explicit product decision — Tawreedat MVP will not use online payment or fixed pricing plans. Company onboarding is: company submits information → admin reviews → admin approves/rejects → payment/collection happens manually via WhatsApp, phone, or email.
  - No payment infrastructure needed for MVP — building a payment gateway/subscription-billing system before there's proven demand would be significant unshipped complexity for no validated benefit.
  - Keeps the door open — `CompanyRegistrationRequest` just captures intent and contact details; nothing about the schema or flow blocks adding real billing later if the business decides it needs one. **Update, same day:** approving a request now creates/updates a `Company` record (see the "Approval creates/updates a Company" decision below), but it still does not activate a public company-profile page or create a company-owner account — that bridge is deliberately left for Phase 2 (see `docs/ROADMAP.md`).
  - Consequence: the pre-existing `/plans` route/view is untouched but now orphaned — no "سجّل شركتك" call-to-action links to it anymore (they point to `/register-company` instead). `/plans` itself is out of scope for this change; see `docs/ROADMAP.md` → Phase 1.

**Decision:** Gate `CompanyRegistrationRequestResource` with a dedicated `view registration requests`/`manage registration requests` permission pair, rather than reusing `view contact requests`/`manage contact requests`.
**Reason:**
  - Consistency with an established convention — every other resource domain in this codebase (news, pages, news categories, contact requests, etc.) already follows one-permission-pair-per-domain. Overloading the contact-request permissions for a conceptually different queue (sales leads vs. general inquiries) would have been the first exception to that pattern for no real simplification.
  - Independent grantability — a future role could plausibly need one queue but not the other (e.g. a sales-only role that only sees registration requests). A shared permission would make that impossible without a later breaking migration.
  - The user's brief explicitly offered both options as acceptable for MVP ("it is acceptable to gate this resource with `view contact requests`/`manage contact requests`"); the dedicated pair was chosen as the more consistent option, not because the shared option was disallowed.
  - Granted to the same roles as the contact-request permissions (Super Admin via `Permissions::all()`, Admin via the existing all-except-user-management rule, and Support explicitly) since company registration requests are new-business/sales leads — within Support's existing "front-line support/sales" remit (see `docs/ADMIN_PANEL.md` → "Roles & Permissions").

**Decision:** Payment and collection for approved company registrations happen manually (WhatsApp, phone, or email) — not built into the product.
**Reason:**
  - Matches the explicit MVP scope decision above — no online payment, no subscription billing, no pricing table anywhere in this flow.
  - Lets the team validate demand and pricing conversations directly with real companies before investing in payment infrastructure.
  - The admin-facing side of this is just a status change (`pending` → `approved`/`rejected`/`contacted`) plus free-text `admin_notes` for whatever context the manual conversation produces — no dedicated "payment" field or state was added, since none of that is tracked in-product for MVP.

**Decision:** Approving a `CompanyRegistrationRequest` creates or updates a real `Company` record, instead of only changing the request's status.
**Reason:**
  - Closes the actual gap this workflow existed to solve — a request that sits "approved" forever with no corresponding `Company` row was never useful on its own; someone would have had to manually re-type the same data into `CompanyResource` anyway. Automating that hand-off is the whole point of collecting structured `city_id`/`category_id`/`logo` data on the request in the first place.
  - `HomeController` already queries `Company::where('status', 'active')` — reusing that existing, unmodified query means an approved company just starts appearing on the homepage with zero frontend changes, rather than needing a second "publish" step or flag.
  - Still stops short of the public Companies Directory/profile pages and company-owner accounts (both explicitly out of scope, still Phase 2 — see `docs/ROADMAP.md`) - creating the `Company` row is necessary but not sufficient for either of those; a `Company` with `status = active` and no directory page is exactly the same "loose end" the homepage already has for its hero CTA (see the Companies-deferral decision above), not a new one.
  - Idempotency was a hard requirement (explicit in the brief: "do not duplicate companies on repeated approval"). `CompanyRegistrationRequestResource::resolveOrCreateCompany()` reuses the linked `company_id` first, then falls back to matching an existing `Company` by slug/email, before ever creating a new row — see `docs/DATABASE_SCHEMA.md` → "CompanyRegistrationRequest".
  - `is_verified`/`is_featured`/`status` are only set on first creation, never overwritten on an update. These are admin-curated flags that live on the `Company` itself once it exists; letting a second approval (e.g. after an admin manually reset a request's status) silently reset them would undo real admin work on the Company record for no reason.

**Decision:** `CompanyRegistrationRequest` exposes its City/Category relationships as `cityRelation()`/`categoryRelation()`, not `city()`/`category()`.
**Reason:**
  - Identical situation, identical fix, to `News::categoryRelation()` (see the 2026-07-03 entries above) — the table keeps legacy free-text `city`/`category` columns for backward compatibility alongside the new `city_id`/`category_id` foreign keys, and Eloquent's attribute resolution always prefers a loaded column over a same-named relation method. Naming the relation `city()` would have meant `$request->city` silently kept returning the old text string forever, never the related `City` model, with no error to catch the mistake.
  - Consistent codebase convention beats a locally "nicer" name — anyone who already knows the `News` precedent immediately understands why this model does the same thing.

**Decision:** The "قبول" (approve) action requires **both** `manage registration requests` and `manage companies`, unlike reject/mark-contacted which only require `manage registration requests`.
**Reason:**
  - Approve is the one action that writes to a different resource's table (`companies`), not just this queue. Someone who can process registration requests (e.g. Support) isn't automatically someone who should be able to create/edit live company listings — that's exactly what `manage companies` already gates for `CompanyResource`.
  - Concretely: Support keeps `view`/`manage registration requests` (see the permission-pair decision above) but was never granted `manage companies`, so Support can view, reject, and mark requests as contacted, but the "قبول" button is hidden for them. Only Super Admin and Admin, who already hold `manage companies`, can complete an approval. This was an explicit requirement in the brief ("Approval action requires: manage registration requests + manage companies if available").

## 2026-07-06

**Decision:** Replace the free-text `position` enum on `Advertisement` with a real `advertisement_slots` table and `advertisement_slot_id` foreign key, rather than just adding more string values to the old enum.
**Reason:**
  - The homepage alone needed 4 distinct ad placements (`headerBanner`/`homeBanner1`/`homeBanner2`/`homeBanner3`) plus 2 more on the news page (`newsSidebarBanner`/`newsFooterBanner`) — 6 total, versus the old enum's 4 loosely-defined values (`header`/`home`/`sidebar`/`footer`) that didn't cleanly map to "one row per placement" (two different existing ads both had `position = 'home'`, an ambiguity the old schema couldn't even express as a data error).
  - A real table means each slot is independently manageable (name, description, recommended dimensions, active/inactive) from its own Filament resource, instead of a hardcoded string list buried in a `Select`'s options array.
  - This was an explicit, detailed spec from the user, including the exact 6 slot keys/names and the exact old→new mapping — this decision documents the *why* behind following it, not an independent judgment call.

**Decision:** Keep the legacy `position` column on `advertisements` rather than dropping it in this pass.
**Reason:**
  - Explicit instruction in the brief ("Do NOT remove the legacy `position` column yet"). It's marked `@deprecated` on `Advertisement::scopeForPosition()` and documented as superseded in `docs/DATABASE_SCHEMA.md`, but not removed — any external code, report, or manual query still reading `position` keeps working unchanged.
  - No functional harm in keeping it — nothing in the new slot-based code reads or writes it anymore (the data migration only reads it once, to backfill `advertisement_slot_id`).

**Decision:** `headerBanner` is deliberately resolved and reused as *both* the sitewide header banner and one of the homepage's four ad cards, rather than giving the homepage a 4th independent slot for that position.
**Reason:**
  - The user's own spec section 7 lists exactly 4 homepage variables (`headerBanner`, `homeBanner1`, `homeBanner2`, `homeBanner3`) while also specifying `header_banner` as a separate, sitewide slot used by the header partial on every page — the only way to reconcile "4 homepage cards" with "6 total slots, one of which is sitewide" is for the homepage to reuse the header's slot as one of its own cards, matching what the pre-existing homepage template already did before this refactor (its "hero" ad card was always the same ad as the header banner).
  - Consequence handled explicitly: since `headerBanner` renders twice in a single homepage request (header partial + homepage card), only one of those two call sites increments the impression counter (see the double-counting fix below) — otherwise every homepage visit would silently double-count that one slot's views relative to the other five.

**Decision:** Impression counting (`views++`) happens in `AppServiceProvider`'s view composers, not inside `AdvertisementManager::slot()` itself.
**Reason:**
  - `AdvertisementManager::slot()`'s result is cached for 5 minutes per slot. If the increment lived inside that cached closure, only the first request in each 5-minute window would ever count — every subsequent (cached) request would silently undercount real page views. Keeping the increment in the composer (which runs on every request, cache hit or not) means every real render is counted, while the slot *lookup* itself stays cheap.

**Decision:** Ad links point at `route('ads.click', $advertisement)` (`GET /ad/{advertisement}`) instead of the ad's `link` field directly, with `AdClickController` incrementing `clicks` before redirecting.
**Reason:**
  - Click tracking has to happen server-side on a real request — there was no other reliable place to count a click without adding frontend JS/beacon tracking, which the brief didn't ask for and would have added complexity disproportionate to the goal.
  - Falls back to `route('home')` if an ad's `link` is empty, so a misconfigured ad never produces a broken/blank redirect for a visitor who clicked a real banner image.

**Decision:** `AdvertisementSlotResource` hardcodes `canDelete()`/`canDeleteAny()` to `false` — slots can be renamed/edited/deactivated but never deleted from the admin panel.
**Reason:**
  - Every slot's `key` (e.g. `header_banner`) is referenced by exact string throughout the codebase — `AdvertisementManager::knownSlotKeys()`, the view composers in `AppServiceProvider`, and the dashboard widget. Deleting a slot record through the UI would silently break a specific frontend ad placement (and orphan any `Advertisement` rows still pointing at it via `nullOnDelete()`) with no warning at the point of deletion. Making the slot list fixed-but-editable avoids that failure mode entirely; if a 7th slot is ever genuinely needed, it should be added as a migration (like the original 6), not through ad-hoc admin deletion/recreation.

**Decision:** `Advertisement::scopeForSlot()` checks the slot's own `is_active` flag, not just the ad's.
**Reason:**
  - `AdvertisementSlotResource`'s form explicitly tells admins that deactivating a slot hides any ad in it ("عند التعطيل لن يظهر أي إعلان في هذه المساحة"). Without this check, `AdvertisementManager::slot()` would keep serving an active ad from a slot the admin just marked inactive, contradicting that helper text — fixed by adding `->where('is_active', true)` to the slot join inside `scopeForSlot()`.

**Decision:** Did not implement ad rotation, a payment/billing system, or advertiser self-serve accounts in this refactor.
**Reason:**
  - Explicitly out of scope per the brief ("Do NOT: ... Build ad rotation. Build payment system. Build advertiser accounts."). The `priority` column exists specifically so a future rotation feature has something to sort/weight by without another migration, but no rotation logic itself was built — `AdvertisementManager::slot()` currently just picks the single highest-priority (then newest) eligible ad per slot, deterministically, on every call.

**Decision:** Ship the Companies Directory (`/companies`) as a simple MVP now, while keeping Company Profile (`/companies/{slug}`) deferred to Phase 2.
**Reason:**
  - This was an explicit, scoped brief: build `/companies` only, using the existing `Company`/`City`/`Category` models, without building profile pages, owner accounts, or payments. The directory's cards were designed from the start to show contact info (phone/email/website) directly rather than linking anywhere, so it doesn't actually depend on Company Profile existing — the original 2026-07-03 decision to bundle both together into one deferred Phase 2 item wasn't a technical dependency, just a product-scope grouping that a later, more specific brief was free to split.
  - Splitting them lets the two most visible "broken-looking" homepage entry points (hero CTA, hero search) start working immediately, without pulling in the larger, still-undecided scope of what a company profile page should even contain.

**Decision:** City and category filters on `/companies` use the model's `slug` column, not its numeric id or raw name.
**Reason:**
  - Explicit in the brief (`?city=الرياض`, `?category=مواد-البناء`). Slugs are also already the stable, human-readable identifier this codebase uses everywhere else for filtering (see `NewsController`'s `category` param against `NewsCategory.slug`), so this keeps the convention consistent rather than introducing a second filtering scheme (by id) for one resource.
  - An unrecognized slug intentionally still applies the `whereHas` filter (rather than being silently ignored), so a stale/typo'd slug in a bookmarked or shared URL yields an honest empty result set instead of quietly showing the unfiltered directory — same reasoning already applied to `NewsController@index`'s category filter.

**Decision:** `CompanyController@index`'s city/category sidebar lists are filtered to only those with at least one currently-active company, rather than showing every active `City`/`Category` row regardless of count.
**Reason:**
  - Matches the existing, established convention from `NewsController@index`'s category sidebar — a filter link that's guaranteed to produce zero results is worse UX than not showing it at all. Counts shown next to each filter option always reflect the full active-company set (not the currently-applied filters), so the sidebar reads as a stable taxonomy rather than a list that reshuffles as someone filters.

**Decision:** The directory's single contact CTA ("تواصل مع الشركة") prefers `tel:` over `mailto:`, and renders nothing if neither exists — it never falls back to a disabled/greyed-out button.
**Reason:**
  - Explicit in the brief's exact fallback order. A phone call is the faster, lower-friction contact method for this audience (construction-sector B2B), so it's preferred when both exist.
  - A disabled button that goes nowhere is worse than no button — matches the same philosophy already applied elsewhere in this codebase (e.g. `QuickActionsWidget` hiding itself entirely rather than showing dead buttons; see the 2026-07-03 dashboard-widget entry above).

**Decision:** Removed the homepage's `openCompany()` Alpine method instead of updating it to point somewhere else.
**Reason:**
  - It built a URL against `route('companies.show')` (the still-placeholder `/companies/sample` route) with a `?company=` query string that route doesn't even accept a parameter for — it was already broken by construction, not just outdated.
  - It was never actually wired to a `@click` handler on any company card (confirmed by searching the template for call sites) — genuinely dead code, not a regression to "fix." Since the brief is explicit that company cards must not link to a missing profile page, deleting this was the correct fix rather than repointing it at `/companies` (which would have implied every card behaves like a single link to the directory itself — a confusing affordance nobody asked for).

**Decision:** Un-commented the homepage's existing "تصفح الشركات" hero button instead of building a new one.
**Reason:**
  - The button was already fully coded and styled (correct classes, correct `@click="go('directory')"` wiring) but wrapped in an HTML comment — it simply never rendered. Since `go('directory')` already resolves to `route('companies.index')`, enabling it was a one-line change that satisfies "تصفح الشركات should link to route('companies.index')" without touching the homepage's visual design, copy, or layout in any other way.
  - Left the second, similarly-commented "عرض جميع الشركات" button (in the companies section header) untouched — enabling it wasn't asked for and risked reading as an unrequested design change; the hero CTA alone was sufficient to satisfy the brief.
