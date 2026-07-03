# Project Status — Tawreedat (توريدات)

Last updated: 2026-07-04

This file tracks what is done and what remains across the frontend, backend, and deployment. Update the checkboxes as work lands — this is the fastest way for anyone (including a future session) to see where the project stands without reading code.

## Frontend

- [x] Home — database-backed (categories, companies, news, ads), empty states, fallback images
- [x] News Index — **production-ready**, database-backed (`NewsController@index`): search, category filter, pagination, category taxonomy sidebar
- [x] News Detail — **production-ready**, database-backed (`NewsController@show`): related news, category taxonomy, SEO fields
- [x] Contact — **production-ready** (moved from "needs backend integration"): form submissions persist to the `ContactRequest` model, are visible in Filament, protected against spam (honeypot + rate limiting), and trigger admin notifications. Verified end-to-end via live browser test on 2026-07-03.
- [x] "سجّل شركتك" (Company Registration Request) — **production-ready**, added 2026-07-04: `/register-company` persists submissions to `CompanyRegistrationRequest` (status `pending`), visible in Filament, protected against spam (honeypot + rate limiting), triggers admin notifications. This is a **request/review workflow, not a self-serve signup, subscription plan, or online payment** — see "Company Registration Requests" below and `docs/DECISIONS.md`.
- [ ] Companies Directory — **Deferred to Phase 2** (product decision, 2026-07-03 — see `docs/DECISIONS.md`). Route (`/companies`) and route name (`companies.index`) exist, but the Blade view does not. Not a launch blocker: v1 scope excludes full company browsing.
- [ ] Company Profile — **Deferred to Phase 2** (same decision). Route (`/companies/sample`) and route name (`companies.show`) exist, but the Blade view does not.
- [ ] About — route exists (`/about` → `pages.about`), page not yet reviewed/finalized
- [ ] Plans — route exists (`/plans` → `pages.plans`), page not yet reviewed/finalized

## Backend

- [x] Laravel setup — Laravel 12, PHP ^8.2
- [x] Tailwind v4 — CSS-first config in `resources/css/app.css`, RTL by default
- [x] Filament installation — Filament v5.6.8 at `/admin`
- [x] Models — City, Category, Company, News, NewsCategory, Advertisement, ContactRequest, Page, SiteSetting, User (roles)
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

**What "approval" now means:** an approved request's `Company` becomes visible in the homepage's active-companies section automatically (same `status = 'active'` query `HomeController` already used) — but there is still no dedicated public company-profile page and no public Companies Directory listing. **Still deferred (unchanged by this work):** the public Companies Directory/Company Profile (Phase 2) and company-owner accounts.

## SEO & Site Settings (completed 2026-07-03)

- [x] Site settings and SEO manager — Filament page (`SiteSettings`) covering site info, SEO defaults, social links, robots, and Open Graph.
- [x] Robots.txt — `/robots.txt`, driven by `robots_indexing_enabled` (defaults **off** for local/dev).
- [x] Sitemap.xml — `/sitemap.xml`, includes home, news index, contact, published static pages, and every published news article.

## News (completed 2026-07-03)

- [x] News categories system — `NewsCategory` model/table, `NewsCategoryResource`, category filtering on `/news`, legacy `news.category` text column kept temporarily for backward compatibility (see `docs/ROADMAP.md`).

## Deployment

- [ ] Production server
- [ ] Domain
- [ ] SSL
- [ ] Queue workers
- [ ] Backups

## Notes

- Two homepage links (`companies.index`, `companies.show`) currently point at routes with no corresponding Blade view. This is a deliberate v1 scope decision, not a bug — Companies Directory and Company Profile are deferred to Phase 2. See `docs/DECISIONS.md` and `docs/ROADMAP.md`. The homepage's hero CTA, hero search, and category links still point at `companies.index`, so those entry points will fail until Phase 2 — this is a known, accepted inconsistency for v1.
- No public-facing authentication exists (Filament admin login is admin-only); this remains a Phase 2 item and is unrelated to the admin roles/permissions system, which is complete. The public contact form persists to the database as of 2026-07-03 — see "Contact Form & Spam Protection" above.
