# Admin Panel — Tawreedat

Last updated: 2026-07-04

## Overview

- **Filament version:** v5.6.8
- **Admin URL:** `/admin` (panel id: `admin`, default panel)
- **Auth:** Filament's built-in login (`->login()`), standard `Authenticate` middleware
- **Locale/RTL:** Driven by the app locale (`APP_LOCALE=ar`); Filament v5 has no `->rtl()` panel method — direction follows the resolved locale automatically.
- **Brand name:** Rendered via a closure view (`filament.branding.logo`), not a plain string
- **Font:** Alexandria (`->font('Alexandria')`)
- **Theme:** Custom CSS via `->viteTheme('resources/css/filament/admin/theme.css')` — dark green (`#063f34`) / gold (`#d4a017`) identity, cream backgrounds, rounded-2xl cards. See theme file for full token list (`--tawreedat-green`, `--tawreedat-green-dark`, `--tawreedat-gold`, `--tawreedat-cream`).
- **Colors registered with Filament:** `primary: #063f34`, `warning: #d4a017`, `accent: #d4a017`

## Dashboard

The default Filament dashboard was replaced with an executive control center (`App\Filament\Pages\Dashboard`, `app/Filament/Pages/Dashboard.php`) built from 7 purpose-built widgets in `app/Filament/Widgets/`. Registered explicitly, in display order, in `AdminPanelProvider::panel()->widgets()`:

| Order | Widget | Type | Purpose |
|---|---|---|---|
| 1 | `KpiOverviewWidget` | Stats overview (full width) | Top-row KPI cards |
| 2 | `QuickActionsWidget` | Custom (full width) | Permission-filtered shortcut buttons |
| 3 | `LatestNewsWidget` | Table | 5 most recent news items |
| 4 | `LatestContactRequestsWidget` | Table | 5 most recent contact requests |
| 5 | `LatestCompanyRegistrationRequestsWidget` | Table | 5 most recent "سجّل شركتك" requests (added 2026-07-04) |
| 6 | `AnalyticsWidget` | Stats overview | Simple period-based activity counts |
| 7 | `SystemStatusWidget` | Custom | SEO/contact/security/environment health |

The old ad-hoc widgets (`WelcomeWidget`, `TawreedatStatsWidget`, `RecentCompaniesWidget`) were removed rather than kept alongside the new set — running both would have duplicated several of the same numbers and worked against the "information density over decoration" goal for this dashboard. `Dashboard::getColumns()` is overridden to `['default' => 1, 'lg' => 2]` so the grid is a single column on small/medium screens and two columns on large screens, rather than the framework default of a flat 2 (which squeezed cards on mobile).

### KPI cards (`KpiOverviewWidget`)

Six cards, each a real `COUNT()` query (never fabricated data), computed once per request and cached together for 5 minutes (`Cache::remember('filament.dashboard.kpi.v2', ...)`) so opening the dashboard doesn't fire the same handful of counts on every page load:

- **إجمالي الأخبار المنشورة** — published news, with a "+N هذا الشهر" trend line.
- **التصنيفات الإخبارية** — total news categories, with an "N نشط" trend line.
- **طلبات التواصل الجديدة** — contact requests with `status = new`, with a "+N هذا الأسبوع" trend line.
- **إجمالي الصفحات المنشورة** — published pages, with a "من إجمالي N صفحة" context line.
- **طلبات تسجيل الشركات قيد المراجعة** (added 2026-07-04) — company registration requests with `status = pending`, with a "+N هذا الأسبوع" trend line.
- **عدد المستخدمين الإداريين** — admin-capable users (any panel role or legacy `is_admin`), **Super Admin only**.

### Latest activity tables

- **`LatestNewsWidget`** ("أحدث الأخبار") — 5 most recent news items (العنوان، التصنيف، الحالة، تاريخ النشر), eager-loading `categoryRelation` to avoid an N+1 per row, with a "تعديل الخبر" row action.
- **`LatestContactRequestsWidget`** ("آخر طلبات التواصل") — 5 most recent contact requests (الاسم، الشركة، نوع الطلب، الحالة، التاريخ), with a "فتح الطلب" row action.
- **`LatestCompanyRegistrationRequestsWidget`** ("آخر طلبات تسجيل الشركات", added 2026-07-04) — 5 most recent "سجّل شركتك" requests (اسم الشركة، مسؤول التواصل، الحالة، التاريخ), with a "فتح الطلب" row action.

### Analytics (`AnalyticsWidget`)

Simple stat cards, deliberately without charts: أخبار هذا الشهر (news created this month, any status — distinct from the KPI card's "published" trend), طلبات التواصل هذا الأسبوع, المستخدمون الإداريون (Super Admin only), الصفحات المنشورة. Cached the same way as the KPI cards (`filament.dashboard.analytics.v1`, 5 minutes).

### Quick Actions (`QuickActionsWidget`)

Shortcut buttons, each independently gated by the same permission its target action requires (so a visible button is never a dead end): إضافة خبر (`manage news`), إضافة صفحة (`manage pages`), إضافة تصنيف خبري (`manage news categories`), عرض طلبات التواصل (`view`/`manage contact requests`), طلبات تسجيل الشركات (`view`/`manage registration requests`, added 2026-07-04), إعدادات الموقع (`manage settings`), إدارة المستخدمين (Super Admin role only, mirroring `UserResource::canViewAny()`). The whole widget hides itself if a user has zero visible actions.

### System Status (`SystemStatusWidget`)

Visible to **Super Admin and Admin only** (gated on the `manage settings` permission — the same permission that governs Site Settings, since this widget surfaces comparable operational information). Every value is a genuine runtime check, not a hardcoded "green light":

- **SEO** — Sitemap/Robots "ready" checks are real route-registration checks (`Route::has('sitemap')` / `Route::has('robots')`), plus the current `robots_indexing_enabled` value from Site Settings.
- **Contact** — "الإشعارات مفعلة" reflects whether at least one admin-capable recipient currently exists (mirrors `ContactController::resolveAdminRecipients()`); "الحماية مفعلة" checks that the `contact.store` route actually has a `throttle:*` middleware attached.
- **Security** — "Roles & Permissions" confirms all 4 expected roles exist in the `roles` table; "Super Admin" confirms at least one user currently holds that role.
- **Environment** — `APP_ENV`, `APP_DEBUG`, `QUEUE_CONNECTION`, `MAIL_MAILER` read via `config()`. If `APP_DEBUG` is `true`, a red warning banner is shown at the top of the widget — this is the one setting on this dashboard that should never be `true` in production (see `docs/DEPLOYMENT_CHECKLIST.md` §1).

### Role-specific visibility

Every widget/card's visibility is **permission-driven**, not role-hardcoded — each one checks the same `Permissions` constants (`app/Support/Permissions.php`) that already gate the corresponding Resource, so it naturally tracks whatever `RolesAndPermissionsSeeder` currently grants each role without needing a second, separately-maintained role list on the dashboard. In practice, given the seeded roles, this produces exactly the intended matrix:

| Role | KPI cards | Quick Actions | Latest News | Latest Contact Requests | Latest Registration Requests | Analytics | System Status |
|---|---|---|---|---|---|---|---|
| Super Admin | all 6 | all 7 buttons | ✓ | ✓ | ✓ | all 4 | ✓ |
| Admin | all except "عدد المستخدمين الإداريين" | all except "إدارة المستخدمين" | ✓ | ✓ | ✓ | all except "المستخدمون الإداريون" | ✓ |
| Editor | content cards only (news/categories/pages) | content buttons only | ✓ | hidden | hidden | content cards only | hidden |
| Support | contact + registration-request cards | contact + registration-request buttons | hidden | ✓ | ✓ | contact card only | hidden |

## Topbar / shell customizations

Implemented entirely through supported Panel APIs — no vendor files edited.

- `PanelsRenderHook::USER_MENU_BEFORE` → `filament.topbar.user-info` (name/role next to the user menu)
- `PanelsRenderHook::GLOBAL_SEARCH_BEFORE` → `filament.topbar.locale-indicator` (locale badge)

## Navigation groups

1. **إدارة الشركات** (Company Management)
2. **المحتوى** (Content)
3. **الإعلانات** (Advertisements)
4. **الطلبات** (Requests)
5. **الإعدادات** (Settings)

## Resources

| Nav group | Resource | Model label | Plural label | Nav label |
|---|---|---|---|---|
| إدارة الشركات | `CompanyResource` | شركة | الشركات | الشركات |
| إدارة الشركات | `CategoryResource` | تصنيف | التصنيفات | التصنيفات |
| إدارة الشركات | `CityResource` | مدينة | المدن | المدن |
| المحتوى | `NewsResource` | خبر | الأخبار | الأخبار |
| المحتوى | `NewsCategoryResource` | تصنيف خبري | التصنيفات الإخبارية | التصنيفات الإخبارية |
| المحتوى | `PageResource` | صفحة | الصفحات | الصفحات |
| الإعلانات | `AdvertisementResource` | إعلان | الإعلانات | الإعلانات |
| الطلبات | `ContactRequestResource` | طلب تواصل | طلبات التواصل | طلبات التواصل |
| الطلبات | `CompanyRegistrationRequestResource` (added 2026-07-04) | طلب تسجيل شركة | طلبات تسجيل الشركات | طلبات تسجيل الشركات |
| الإعدادات | `SiteSettings` (Page, not Resource) | — | — | إعدادات الموقع |
| الإعدادات | `UserResource` | مستخدم | المستخدمون | المستخدمون |

All resources are auto-discovered from `app/Filament/Resources` (`discoverResources`), so a new resource dropped into that folder with the correct namespace will appear without further Panel config. Custom Pages (`SiteSettings`) are similarly auto-discovered from `app/Filament/Pages`.

## Roles & Permissions

Access control uses `spatie/laravel-permission`. Deliberately simple for MVP: 4 fixed roles, 15 fixed permissions (13 original + `view registration requests`/`manage registration requests` added 2026-07-04), no custom role builder or per-record ownership — see `docs/DECISIONS.md` for why this scope was chosen over a full RBAC package.

**Two separate gates exist and should not be confused:**

1. **Panel entry** (`User::canAccessPanel()`) — can this user log into `/admin` at all? Requires one of the four roles below, **or** the legacy `is_admin = true` flag (see "is_admin is transitional" below).
2. **Resource/page authorization** (`canViewAny()`/`canCreate()`/`canEdit()`/`canDelete()` on each Resource, `canAccess()` on `SiteSettings`) — once inside the panel, what can this user actually see and do? Governed entirely by permissions, not by `is_admin`.

### Roles

| Role | Intended for | Permissions |
|---|---|---|
| **Super Admin** | Platform owner/technical lead | All 15 permissions, including `manage users` |
| **Admin** | Day-to-day operator | All permissions **except** `view users` / `manage users` — cannot see or touch the Users resource |
| **Editor** | Content writer/editor | `view content`, `manage news`, `manage pages`, `manage news categories` |
| **Support** | Front-line support/sales | `view contact requests`, `manage contact requests`, `view registration requests`, `manage registration requests` (registration requests added 2026-07-04 — they're new-business leads, squarely within this role's remit) |

### Permissions

| Group | Permissions |
|---|---|
| Companies | `view companies`, `manage companies` |
| Content | `view content`, `manage news`, `manage pages`, `manage news categories` |
| Ads | `view ads`, `manage ads` |
| Requests (contact) | `view contact requests`, `manage contact requests` |
| Requests (company registration) | `view registration requests`, `manage registration requests` (added 2026-07-04) |
| Settings | `manage settings` |
| Users | `view users`, `manage users` |

Seeded by `database/seeders/RolesAndPermissionsSeeder.php` (idempotent — `firstOrCreate` for roles/permissions, `syncPermissions` for role→permission assignment, safe to re-run). Which resources check which permission is documented inline on each Resource class; as a summary: `CompanyResource` → companies permissions, `NewsResource`/`PageResource`/`NewsCategoryResource` → `view content` + their respective `manage *` permission, `AdvertisementResource` → ads permissions, `ContactRequestResource` → contact request permissions, `CompanyRegistrationRequestResource` → registration request permissions, `SiteSettings` → `manage settings`, `UserResource` → hardcoded to the **Super Admin role directly** (not a permission — see below).

Registration requests deliberately got their **own** permission pair (`view`/`manage registration requests`) rather than reusing `manage contact requests`, matching the one-permission-pair-per-resource pattern every other domain in this table follows — see `docs/DECISIONS.md`.

`CategoryResource` (company categories) and `CityResource` are **not** permission-gated as of this system — any panel user who can get in can manage them. Not an oversight for this MVP: no permission was defined for them per the original spec, and they're low-risk, low-volume reference data.

### User management (`UserResource`)

Deliberately stricter than the generic permission system: every authorization method on `UserResource` checks `auth()->user()->hasRole('Super Admin')` directly, not a permission. This is intentional — user/role management is the one place in the panel where a mistake (or a compromised Admin account) could escalate to full control, so it isn't left to the generic permission grid. Additional hardcoded safety rules, layered on top of the Super-Admin-only gate:

- **A user can never delete their own account** — checked in `UserResource::canDelete()`, again independently in the Edit page's header Delete action, and again via `authorizeIndividualRecords()` on the bulk-delete action (so selecting your own row alongside others in a bulk delete doesn't bypass the check).
- **The resource is fully hidden from non-Super-Admins** — `canViewAny()` returning `false` means it doesn't appear in navigation and the URL isn't reachable, not just "buttons are hidden."

### is_admin is transitional

The `users.is_admin` boolean predates this roles/permissions system (added when the only requirement was "keep non-admins out of `/admin`"). It is kept **only** so any account created before roles existed doesn't get locked out, and is checked purely for panel entry (`canAccessPanel()`) — it grants **no** permissions and is not read anywhere in resource/page authorization. New accounts should be given a role instead of relying on it. It may be removed in a future migration once every real admin account has a role assigned and nothing checks `is_admin` for panel entry anymore — search the codebase for `is_admin` before doing so.

## User Management

Quick-reference summary; full detail is in "Roles & Permissions" above.

- **Roles:** Super Admin, Admin, Editor, Support.
- **Who can access `/admin` at all:** any user with one of the four roles above, **or** the legacy `is_admin = true` flag (transitional — see below). Enforced in `User::canAccessPanel()`. Everyone else is denied panel entry outright, regardless of whether they have a valid login.
- **Who can manage users:** Super Admin only. Every authorization method on `UserResource` checks `auth()->user()->hasRole('Super Admin')` directly — not the `manage users`/`view users` permissions, even though those permissions exist and are seeded. Admin, Editor, and Support cannot see or reach the Users resource at all (`canViewAny()` returns `false`, hiding it from navigation and blocking the URL directly).
- **Self-delete protection:** a user can never delete their own account. Enforced independently in three places: `UserResource::canDelete()`, the Edit page's header Delete action visibility, and `authorizeIndividualRecords()` on the bulk-delete action (so selecting your own row alongside others in a bulk delete doesn't bypass the check).
- **Super Admin protection:** a non-Super-Admin cannot edit or delete a Super Admin account, even if they somehow gained access to the Users resource.
- **is_admin compatibility (transitional):** `is_admin` is checked only for panel *entry*, never for resource/page permissions. It exists solely so accounts created before the roles/permissions system landed aren't locked out. New accounts should always be assigned a role instead. See "is_admin is transitional" above and `docs/DECISIONS.md`.

## Contact Management

Public contact form submissions (`/contact`) are persisted to `ContactRequest` and appear in the admin panel under **طلبات التواصل** (نav group: الطلبات → `ContactRequestResource`).

Capabilities:

- **View** — gated by `view contact requests` or `manage contact requests` (`canViewAny()`). Super Admin, Admin, and Support all qualify by default.
- **Edit status** — gated by `manage contact requests` (`canEdit()`). Status values: جديد (new), قيد المعالجة (in progress), تم الحل (resolved), مغلق (closed).
- **Receive notifications** — every user who currently qualifies to view/manage contact requests (plus any legacy `is_admin = true` account) is emailed `NewContactRequestNotification` synchronously on every new valid submission via `ContactController::resolveAdminRecipients()`. Notification failures are logged and never block the visitor's submission. See `docs/DEPLOYMENT_CHECKLIST.md` for the mail configuration this depends on in production.

Spam protection (honeypot + `throttle:5,1` rate limiting on `POST /contact`) is documented in `docs/PROJECT_STATUS.md` and `docs/DECISIONS.md`, and was verified end-to-end via a live browser test on 2026-07-03.

## Company Registration Request Management (added 2026-07-04, extended 2026-07-04)

"سجّل شركتك" is a **request/review workflow, not a self-serve signup, subscription plan, or online payment** — see `docs/DECISIONS.md`. Public submissions (`/register-company`) are persisted to `CompanyRegistrationRequest` with `status = pending` and appear in the admin panel under **طلبات تسجيل الشركات** (nav group: الطلبات → `CompanyRegistrationRequestResource`).

Capabilities:

- **View** — gated by `view registration requests` or `manage registration requests` (`canViewAny()`). Super Admin, Admin, and Support all qualify by default.
- **Edit** — gated by `manage registration requests` (`canEdit()`). All submitted fields are editable (not read-only), matching `ContactRequestResource`'s pattern, plus an admin-only `admin_notes` field and a `status` select. City/category are edited as real relationship `Select`s (`cityRelation`/`categoryRelation`, backed by `city_id`/`category_id`) rather than free text, so an admin can correct/complete these before approving. The original free-text submission (if any) is shown as helper text under each select. The logo can be replaced from the same form, and a read-only "سجل الشركة" field links straight to the linked `Company` once one exists.
- **Approve** — converts the request into a real `Company` record (create-or-update; see "Approval → Company conversion" below). Requires **both** `manage registration requests` **and** `manage companies` — this is a deliberately higher bar than reject/mark-contacted, since it writes to the `companies` table, not just this one. Today only Super Admin and Admin hold `manage companies`; Support can still see and reject/contact requests but cannot approve one (see `docs/DECISIONS.md`).
- **Reject / Mark as contacted** — gated by `manage registration requests` only; update `status` and stamp `reviewed_at`/`reviewed_by`. Never touch `companies`.
- All three actions require confirmation and hide themselves once the record is already in that status (e.g. "قبول" disappears once approved).
- **Status values:** قيد المراجعة (`pending`), مقبول (`approved`), مرفوض (`مرفوض`, `rejected`), تم التواصل (`contacted`) — see `CompanyRegistrationRequest::STATUSES`.
- **Receive notifications** — every user who currently qualifies to view/manage registration requests (plus any legacy `is_admin = true` account) is emailed `NewCompanyRegistrationRequestNotification` on every new valid submission, and `CompanyRegistrationRequestApprovedNotification` whenever a request is approved. Both are sent synchronously and wrapped in try/catch — failures are logged and never block the visitor's submission or the admin's approval. Company-facing email is intentionally not sent — see `docs/DECISIONS.md`.

Spam protection (`hp_check` honeypot + `throttle:5,1` rate limiting on `POST /register-company`) mirrors the public contact form exactly — see `docs/PROJECT_STATUS.md` and `docs/DECISIONS.md`.

### Approval → Company conversion (added 2026-07-04)

Clicking "قبول" (`CompanyRegistrationRequestResource::approve()`) does the following, in order:

1. Resolves the target `Company` — reuses the one already linked via `company_id` if set, otherwise tries to match an existing `Company` by the slug `company_name` would generate or by email, otherwise builds a new unsaved `Company`. This is what makes repeated approval idempotent (no duplicate companies).
2. Copies `name`, `description`, `website`, `phone`, `email`, `city_id`, `category_id` onto it, and the `logo` if the request has one (an update never blanks out an existing Company's logo just because the request lacks one).
3. Only for a **brand-new** Company: sets `is_verified = false`, `is_featured = false`, `status = 'active'`. These three are admin-curated flags on the Company itself and are deliberately left untouched on an update, so re-approving a request can't silently un-verify or un-feature a company an admin has since curated.
4. Saves the `Company`, then updates the request: `company_id`, `status = 'approved'`, `reviewed_at`, `reviewed_by`.

Because the homepage already queries `Company::where('status', 'active')` (see `docs/DATABASE_SCHEMA.md`), a newly-approved company appears there automatically — no separate "publish" step, and still no dedicated public company-profile page (Companies Directory remains deferred to Phase 2).

## Status/option fields exposed in the admin UI

See `docs/DATABASE_SCHEMA.md` for the canonical list of enum-like `status`/`position` values used by each resource's `Select` fields and filters.

## Uploads

`CompanyResource` (`logo`), `NewsResource` (`image`), `AdvertisementResource` (`image`), and `CompanyRegistrationRequestResource` (`logo`, added 2026-07-04) all use Filament `FileUpload` fields explicitly pinned to `->disk('public')`. The public `/register-company` form's own logo upload (also `public` disk) stores under `company-registration-logos/`, separately from `CompanyResource`'s `companies/logos/` — the two are only merged onto the same `Company.logo` value when a request is approved. `php artisan storage:link` must be run once per environment so `storage/app/public` is reachable at `public/storage`.

## Constraints followed while building this panel

- No vendor files were edited.
- No third-party UI packages were installed.
- No non-functional buttons/controls were added — every visible control in the custom topbar/dashboard is wired to real behavior (working logout/user dropdown, real widget data).
