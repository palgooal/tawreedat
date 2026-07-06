# Project Status — Tawreedat (توريدات)

Last updated: 2026-07-06 (Companies Directory MVP added same day, after the advertising slot refactor)

This file tracks what is done and what remains across the frontend, backend, and deployment. Update the checkboxes as work lands — this is the fastest way for anyone (including a future session) to see where the project stands without reading code.

## Frontend

- [x] Home — database-backed (categories, companies, news, ads), empty states, fallback images
- [x] News Index — **production-ready**, database-backed (`NewsController@index`): search, category filter, pagination, category taxonomy sidebar
- [x] News Detail — **production-ready**, database-backed (`NewsController@show`): related news, category taxonomy, SEO fields
- [x] Contact — **production-ready** (moved from "needs backend integration"): form submissions persist to the `ContactRequest` model, are visible in Filament, protected against spam (honeypot + rate limiting), and trigger admin notifications. Verified end-to-end via live browser test on 2026-07-03.
- [x] "سجّل شركتك" (Company Registration Request) — **production-ready**, added 2026-07-04: `/register-company` persists submissions to `CompanyRegistrationRequest` (status `pending`), visible in Filament, protected against spam (honeypot + rate limiting), triggers admin notifications. This is a **request/review workflow, not a self-serve signup, subscription plan, or online payment** — see "Company Registration Requests" below and `docs/DECISIONS.md`.
- [x] Companies Directory — **Complete / MVP simple directory**, added 2026-07-06: `/companies` (`CompanyController@index`) lists active companies with search (name/description/phone/email/website/city/category), city/category filters (by slug), verified/featured filters, featured-then-verified-then-newest sorting, and 12-per-page pagination. The MVP directory does not include company profiles yet — cards show contact information (phone/email/website) directly instead of linking anywhere. See `docs/FRONTEND_PAGES.md` and `docs/DECISIONS.md`.
- [ ] Company Profile — **Still Deferred to Phase 2**. Route (`/companies/sample`) and route name (`companies.show`) exist as a placeholder, but no Blade view backs it and nothing links to it. Building the real `/companies/{slug}` profile page remains Phase 2 scope.
- [ ] About — route exists (`/about` → `pages.about`), page not yet reviewed/finalized
- [ ] Plans — route exists (`/plans` → `pages.plans`), page not yet reviewed/finalized

## Backend

- [x] Laravel setup — Laravel 12, PHP ^8.2
- [x] Tailwind v4 — CSS-first config in `resources/css/app.css`, RTL by default
- [x] Filament installation — Filament v5.6.8 at `/admin`
- [x] Models — City, Category, Company, News, NewsCategory, Advertisement, AdvertisementSlot (added 2026-07-06), ContactRequest, Page, SiteSetting, User (roles)
- [x] Migrations — all domain tables created (see `docs/DATABASE_SCHEMA.md`)
- [x] Seeders — `TawreedatDemoSeeder`, `RolesAndPermissionsSeeder`, `SiteSettingSeeder`, `NewsCategorySeeder`, `PageSeeder` (all idempotent) wired into `DatabaseSeeder`
- [x] Frontend DB integration — Home, News, and Contact are all wired to the database. Companies Directory/Company Profile remain deferred to Phase 2 (not v1 scope).
- [ ] Authentication — no public-facing auth yet (Filament admin login exists, but that's admin-only)
- [x] Permissions — `spatie/laravel-permission` installed and seeded: 4 roles (Super Admin, Admin, Editor, Support), 15 permissions (13 original + `view registration requests`/`manage registration requests` added 2026-07-04). All Filament resources are permission-gated except `CategoryResource`/`CityResource` (deliberately left open to any panel user — low-risk reference data, see `docs/ADMIN_PANEL.md`). See "Security & Admin Access" below.

## Security & Admin Access (completed 2026-07-03)

- [x] Filament security hardening — `User::canAccessPanel()` gate blocks anyone without a panel role or the legacy `is_admin` flag; no authenticated user gets `/admin` access by default anymore.
- [x] is_admin protection — `is_admin` is checked only for panel *entry*; it grants no resource/page permissions and is documented as transitional (see `docs/DECISIONS.md`).
- [x] Roles & Permissions (Spatie) — `spatie/laravel-permission` seeded via `RolesAndPermissionsSeeder` (idempotent), every existing Filament resource gated by permission.
- [x] User management — `UserResource`, Super-Admin-only visibility, self-delete blocked at three independent layers, Super Admin accounts protected from non-Super-Admin edits/deletes.
- [x] Super Admin role — all 15 permissions, including `manage users`.
- [x] Admin role — all permissions except `view users`/`manage users`.
- [x] Editor role — content permissions only (`view content`, `manage news`, `manage pages`, `manage news categories`).
- [x] Support role — contact-request and company-registration-request permissions (`view`/`manage contact requests`, `view`/`manage registration requests`) — company registration requests are new-business leads, within this role's "front-line support/sales" remit.

## Contact Form & Spam Protection (completed 2026-07-03)

- [x] Contact form persistence — `ContactController@store` saves every valid submission to `ContactRequest`.
- [x] Contact notifications — `NewContactRequestNotification` mailed synchronously to all admin-capable users, failures logged and swallowed (never blocks the visitor).
- [x] Honeypot protection — hidden `hp_check` field; a filled value fakes success without saving or notifying.
- [x] Rate limiting — `POST /contact` throttled to 5 requests/minute per IP (`throttle:5,1`).

Contact submissions are now:
- persisted to the database
- visible in Filament (طلبات التواصل)
- protected against spam
- rate limited
- notifying administrators

Verified end-to-end via a live browser test against the running dev server on 2026-07-03: valid submission (record created, notification logged, success message shown), honeypot submission (fake success, no record, no notification), and rate limiting (5th+ rapid request returned HTTP 429).

## Company Registration Requests (completed 2026-07-04, extended 2026-07-04)

- [x] "سجّل شركتك" is a request/review workflow — `/register-company` persists to `CompanyRegistrationRequest` with `status = pending`; nothing is auto-approved and no company-owner account is created.
- [x] City/category are now real dropdowns — `/register-company` selects from active `cities`/`categories` (`city_id`/`category_id`), with the old free-text `city`/`category` columns kept as a read-only fallback/snapshot.
- [x] Optional logo upload — `/register-company` accepts an image (max 2MB), stored on the `public` disk under `company-registration-logos/`.
- [x] Admin review — `CompanyRegistrationRequestResource` (طلبات التواصل → طلبات تسجيل الشركات) with approve/reject/mark-as-contacted row actions, each stamping `reviewed_at`/`reviewed_by`. Admin can adjust the city/category/logo before approving.
- [x] **Approval creates/updates a real `Company` record** — approving a request (requires `manage registration requests` **and** `manage companies`) creates a new `Company` (`status = active`, unverified/unfeatured by default) or updates the one already linked, copying name/description/website/phone/email/city/category/logo. Re-approving never duplicates the Company. See `docs/DATABASE_SCHEMA.md` → "CompanyRegistrationRequest" and `docs/ADMIN_PANEL.md`.
- [x] Notifications — `NewCompanyRegistrationRequestNotification` on submission and `CompanyRegistrationRequestApprovedNotification` on approval, both mailed synchronously to admin-capable users, failures logged and swallowed.
- [x] Honeypot + rate limiting — same `hp_check` pattern and `throttle:5,1` as the public contact form.
- [x] Dashboard — KPI card ("طلبات تسجيل الشركات قيد المراجعة") and a "آخر طلبات تسجيل الشركات" latest-activity table, both permission-gated.

**Payment/collection is explicitly manual** (WhatsApp, phone, or email) — there is no online payment, no subscription-plan billing, and no pricing table anywhere in this flow. The pre-existing `/plans` route/page is untouched but is no longer linked from any "سجّل شركتك" call-to-action. See `docs/DECISIONS.md`.

**What "approval" now means:** an approved request's `Company` becomes visible in the homepage's active-companies section automatically (same `status = 'active'` query `HomeController` already used), and — as of 2026-07-06 — also appears in the public Companies Directory (`/companies`) once its `status` is `active`. **Still deferred (unchanged by this work):** the public Company Profile page (Phase 2) and company-owner accounts.

## Companies Directory (MVP, completed 2026-07-06)

- [x] `/companies` (`CompanyController@index`) — lists `Company` records where `status = active`, eager-loading `city`/`category` to avoid an N+1 per card.
- [x] Search (`q`) — matches `name`, `description`, `phone`, `email`, `website`, `city.name`, or `category.name`.
- [x] Filters — `city` and `category` (both by **slug**, e.g. `?city=الرياض`, `?category=مواد-البناء`), `verified` and `featured` (boolean checkboxes).
- [x] Sorting — featured first, then verified first, then newest first (`orderByDesc('is_featured')->orderByDesc('is_verified')->latest()`).
- [x] Pagination — 12 per page, `->withQueryString()` so filters survive page navigation.
- [x] Sidebar — active cities and active categories, each with a live count of active companies, filtered to only those with at least one match (same convention `NewsController`'s category sidebar already uses) — plus total active/verified/featured counts.
- [x] Company card — logo (falls back to `assets/images/company-placeholder.png` if no logo stored), name, category, city, verified/featured badges, short description, phone/email/website links, and a single primary CTA ("تواصل مع الشركة") that links `tel:` if a phone exists, else `mailto:` if an email exists, else no button at all.
- [x] Empty state — "لا توجد شركات مطابقة لهذه المعايير." with a "مسح الفلاتر" button back to the unfiltered index.
- [x] SEO — dedicated `$seoTitle`/`$seoDescription` (not the site-wide defaults).
- [x] Homepage wiring — the previously-commented-out "تصفح الشركات" hero button now renders and links to `route('companies.index')`; the dead `openCompany()` Alpine method (which built a URL to the still-unbuilt `companies.show` route) was removed since it was never actually wired to a `@click` on any card. Nav/footer "الشركات" links were already correct.
- [ ] **Deliberately not built in this pass**: the `/companies/{slug}` company profile page — see "Company Profile" above and `docs/DECISIONS.md`. Directory cards show contact info directly instead of linking to a profile.
- [ ] **Not run yet in this environment**: `php artisan route:list`, `optimize:clear`, `npm run build` — see "Notes" below.

## SEO & Site Settings (completed 2026-07-03)

- [x] Site settings and SEO manager — Filament page (`SiteSettings`) covering site info, SEO defaults, social links, robots, and Open Graph.
- [x] Robots.txt — `/robots.txt`, driven by `robots_indexing_enabled` (defaults **off** for local/dev).
- [x] Sitemap.xml — `/sitemap.xml`, includes home, news index, contact, published static pages, and every published news article.

## News (completed 2026-07-03)

- [x] News categories system — `NewsCategory` model/table, `NewsCategoryResource`, category filtering on `/news`, legacy `news.category` text column kept temporarily for backward compatibility (see `docs/ROADMAP.md`).

## Advertising System (slot-based refactor, 2026-07-06)

- [x] Replaced the free-text `position` column (`header`/`home`/`sidebar`/`footer`) with a proper `advertisement_slots` table — 6 explicit slots, each independently manageable from `AdvertisementSlotResource`: `header_banner`, `home_banner_1`, `home_banner_2`, `home_banner_3`, `news_sidebar`, `news_footer`.
- [x] Data migration mapped every existing ad's old `position` to the matching slot (`header→header_banner`, `home→home_banner_1`, `footer→home_banner_3`, `sidebar→news_sidebar`), only where `advertisement_slot_id` was still null — no existing ad was lost or overwritten.
- [x] Legacy `position` column **kept** (not dropped) for backward compatibility, per explicit scope decision — see `docs/DECISIONS.md`.
- [x] `AdvertisementManager::slot()` — single cached (5 min) lookup point for "which ad is active in slot X right now", used by view composers, the click-redirect controller, and the dashboard widget.
- [x] View composers in `AppServiceProvider` resolve `headerBanner`, `homeBanner1/2/3`, `newsSidebarBanner`, `newsFooterBanner` — no ad queries inside any Blade file.
- [x] Frontend is visually unchanged — same banner positions/markup on the homepage, header, and news article page; only the data source and link targets changed.
- [x] Click tracking — every ad link now routes through `GET /ad/{advertisement}` (`ads.click`), which increments `clicks` then redirects to the ad's real `link` (or home if empty).
- [x] Impression tracking — `views` incremented once per render by the view composers (not inside the cached `AdvertisementManager::slot()` call, to avoid undercounting), with explicit de-duplication for `headerBanner` since it's reused on both the sitewide header and the homepage.
- [x] `AdvertisingStatusWidget` — dashboard widget showing ✓/✗ per slot, total active ads, total clicks, total impressions (Super Admin/Admin only). See `docs/ADMIN_PANEL.md`.
- [ ] **Not run yet in this environment**: `php artisan migrate`, `db:seed`, `optimize:clear`, `npm run build` — no PHP CLI/reachable MySQL in the assistant's sandbox; must be run locally by whoever has the dev environment before the new slots/columns exist in the actual database. See "Notes" below.
- **Explicitly out of scope for this refactor** (per spec): ad rotation, a payment system, or advertiser self-serve accounts.

## Deployment

- [ ] Production server
- [ ] Domain
- [ ] SSL
- [ ] Queue workers
- [ ] Backups

## Notes

- **`companies.index` is now a real, working directory** (as of 2026-07-06) — the homepage's hero CTA, hero search, and footer/nav links all resolve correctly. `companies.show` (`/companies/sample`) remains an intentional Phase 2 placeholder with no real Blade view and nothing links to it anymore (see "Companies Directory" above and `docs/DECISIONS.md`).
- No public-facing authentication exists (Filament admin login is admin-only); this remains a Phase 2 item and is unrelated to the admin roles/permissions system, which is complete. The public contact form persists to the database as of 2026-07-03 — see "Contact Form & Spam Protection" above.
- The 2026-07-06 advertising slot refactor's migrations/seeders have **not been executed against the real database yet** — code changes are complete, but `php artisan migrate && php artisan db:seed && php artisan optimize:clear && npm run build` still need to be run locally. Until then, the new `advertisement_slots`/`advertisement_slot_id`/`views`/`clicks` columns don't exist in the actual database and the admin panel's Advertisement/AdvertisementSlot resources will error if opened.
- The Companies Directory added the same day needs no new migrations (it only reads existing `companies`/`cities`/`categories` tables), but still needs `php artisan route:list`, `optimize:clear`, and `npm run build` run locally to confirm the route resolves and compiled assets are current — same sandbox limitation as above (no PHP CLI, no reachable MySQL).
