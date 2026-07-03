# Deployment Checklist — Tawreedat

Last updated: 2026-07-03

Run through this in order before pointing a real domain at this application. Nothing here is automated — every item must be checked by hand (or wired into your own deploy pipeline). This checklist exists because several of these items (`APP_DEBUG`, the demo admin account, `robots_indexing_enabled`) have already been flagged as real risks if skipped — see the production-readiness audit in this project's history.

## 0. Application-level readiness (verified 2026-07-03)

The items below describe what the **codebase itself** is capable of, verified against the local dev environment (including a live end-to-end browser test of the contact form). They are **not** a substitute for sections 1–8 below — every box in this section still requires a real, deliberate action on the actual production server/environment before launch. Nothing here should be read as "production is already configured."

**Security** — mechanism exists and is verified locally; still requires manual action per environment:
- ☐ `APP_DEBUG=false` — must be set explicitly per environment (see §1). Not yet applicable locally (local dev runs with debug on).
- ☐ `APP_ENV=production` — must be set explicitly per environment (see §1).
- ☑ At least one Super Admin user exists — verified locally (`test@example.com`, seeded via `DatabaseSeeder`, gated to `local` env only). A **real** production Super Admin must still be created deliberately (see §2) — the local demo account must never exist in production.
- ☐ Test users removed from production — not yet applicable (no production deploy exists yet); remains a required check at every deploy (see §2).
- ☑ Roles seeded — `RolesAndPermissionsSeeder` exists, is idempotent, and is wired into `DatabaseSeeder`; running `db:seed` in any environment creates/updates all 4 roles and 13 permissions.

**Mail** — mechanism exists and is verified locally:
- ☑ `MAIL_*` configuration is read and used correctly by `NewContactRequestNotification` — confirmed via `MAIL_MAILER=log` locally. Real SMTP/provider credentials still must be set per environment (see §5) — nothing is configured for production yet.
- ☑ Contact notifications tested — verified end-to-end on 2026-07-03: a live form submission produced a matching log entry (`storage/logs/laravel.log`) containing the correct recipient-resolution logic and message content, with zero delivery failures logged.

**SEO** — mechanism exists and is verified locally:
- ☐ `robots_indexing_enabled=true` — defaults to **false** everywhere, including local. This is a deliberate per-environment, manual flip before launch (see §4) — do not check this box in this document; check it in Filament → Site Settings on the actual production instance.
- ☑ `sitemap.xml` verified — confirmed locally: valid XML, includes home, news index, contact, published static pages, and every published news article (companies intentionally excluded — deferred to Phase 2).
- ☑ Open Graph image field exists and is wired — `default_og_image` in Filament Site Settings feeds the OG/Twitter meta tags via the layout's SEO block. **Not yet filled in** for this project — still required before launch (see §4).

**Admin** — mechanism exists and is verified locally:
- ☑ User management tested — `UserResource` confirmed Super-Admin-only, self-delete protection and Super Admin protection verified by design/code review (see `docs/ADMIN_PANEL.md`).
- ☑ Contact requests visible — confirmed live: a submitted contact request appeared correctly under طلبات التواصل in Filament, viewable by a `Support`-role account.
- ☑ Permissions verified — confirmed live: viewing طلبات التواصل as a `support`-role user worked as expected, consistent with the seeded role/permission matrix.

## 1. Environment configuration (`.env`)

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` — **critical**. If left `true`, any uncaught exception shows a full Whoops stack trace (file paths, env values, query log) to anonymous visitors, and the custom error pages (404/403/419/429/500/503) will never render because Whoops takes over first.
- [ ] `APP_URL` set to the real production domain, with the correct scheme (`https://...`). This feeds `url()`, canonical tags, Open Graph URLs, and the sitemap — if it's wrong, every generated absolute URL on the site is wrong.
- [ ] `APP_KEY` generated for this environment specifically (`php artisan key:generate`) — never reuse a key from local/staging.

## 2. Database

- [ ] Production DB credentials set (`DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and confirmed reachable from the deploy target.
- [ ] Run migrations non-interactively: `php artisan migrate --force`
- [ ] Seed **only** safe, idempotent, content-only seeders — never the full `db:seed` default run without checking what it includes first. As of this checklist, `DatabaseSeeder` gates the demo/test user behind `app()->environment('local')`, so running `db:seed` in production will **not** create `test@example.com` as long as `APP_ENV=production` is actually set correctly (see §1). Still: read `database/seeders/DatabaseSeeder.php` before running it against production, every time, in case that changes later.
- [ ] Confirm no demo/test user exists in the production `users` table after seeding (`SELECT email FROM users;`). If `test@example.com` (or any account you don't recognize) is present, delete it immediately and audit how it got there.
- [ ] Confirm `RolesAndPermissionsSeeder` has run (part of `DatabaseSeeder`) — all 4 roles (Super Admin, Admin, Editor, Support) and 13 permissions should exist (`SELECT name FROM roles;`).
- [ ] Create the real production admin account deliberately — either `php artisan make:filament-user` or a manual `User::create()` — and assign it the **Super Admin** role (`$user->assignRole('Super Admin')`). Setting `is_admin = true` alone still grants panel entry (transitional compatibility — see `docs/ADMIN_PANEL.md`), but a role is what grants actual resource permissions, so use the role, not just the flag, for the real production account. No admin account is created automatically for production.

## 3. File storage

- [ ] `php artisan storage:link` run on the production host, so uploaded company logos, news images, advertisement images, and the Site Settings OG image resolve to public URLs.
- [ ] Confirm the `storage/app/public` directory (or your configured disk) is writable by the web server user and has adequate free space — there are currently no upload size limits enforced at the application level, so disk usage should be monitored.

## 4. SEO / indexing

- [ ] `robots_indexing_enabled` turned **on** in الإعدادات → إعدادات الموقع (Filament → Site Settings) before launch. It defaults to **off** (seeded `false` for local/dev use, per `SiteSettingSeeder`) specifically so a half-built site never gets indexed by accident — this must be a deliberate, manual flip, not something that happens automatically on deploy.
- [ ] Verify `/robots.txt` reflects `Allow: /` and includes the `Sitemap:` line once indexing is enabled.
- [ ] Verify `/sitemap.xml` returns valid XML and lists the expected URLs (home, news index, contact, about/plans/privacy/terms, published news articles).
- [ ] Fill in `default_seo_title`, `default_seo_description`, and `default_og_image` in Site Settings — these are the fallback for every page that doesn't set its own SEO fields.
- [ ] Set `google_search_console_verification` and `google_analytics_id` in Site Settings if/when those accounts exist for this domain.

## 5. Queue & mail

- [ ] `QUEUE_CONNECTION` set to a real queue driver (`database`, `redis`, etc.) appropriate for production load, and a queue worker process running (e.g. via Supervisor or your host's process manager) — `database` alone with no worker running means anything queued silently never executes.
- [ ] **Mail must be configured before launch — this is no longer optional.** The contact form (`ContactController@store`) sends `App\Notifications\NewContactRequestNotification` to admin users on every valid submission. With the default `.env.example` value `MAIL_MAILER=log`, that notification is silently written to the log file instead of actually emailed — submissions still save correctly (mail failures are caught and logged, never block the user), but **no admin will be alerted** to new leads. Set all of the following for real production mail delivery:
  - [ ] `MAIL_MAILER` (e.g. `smtp`, or a transactional provider's Laravel driver — not `log`)
  - [ ] `MAIL_HOST`
  - [ ] `MAIL_PORT`
  - [ ] `MAIL_USERNAME`
  - [ ] `MAIL_PASSWORD`
  - [ ] `MAIL_FROM_ADDRESS` (and `MAIL_FROM_NAME` — currently defaults to `${APP_NAME}`)
- [ ] Send a real test email through the configured mailer before relying on it (e.g. `php artisan tinker` → `Notification::route('mail', 'you@example.com')->notify(new \App\Notifications\NewContactRequestNotification(\App\Models\ContactRequest::first()))`, or just submit the live contact form once after deploy).
- [ ] Confirm at least one real admin user exists with either the `Super Admin`/`Admin`/`Support` role or `is_admin = true` — `resolveAdminRecipients()` in `ContactController` only sends to users who currently qualify for one of those, and silently sends to no one if none exist (by design, so a misconfigured admin list never breaks a visitor's submission — but it also means nobody gets notified until at least one qualifying admin exists).
- [ ] Confirm rate limiting is in effect: `POST /contact` is throttled to 5 requests/minute per IP (`throttle:5,1` in `routes/web.php`). A 6th submission within a minute from the same IP should return HTTP 429.

## 6. Backups

- [ ] Automated database backups configured (frequency appropriate to how often content changes — daily at minimum).
- [ ] Automated backup of the `storage/app/public` uploads directory (logos, news images, ad images, OG images) — these are not in the database and won't be covered by a DB-only backup.
- [ ] Confirm at least one backup has actually been restored successfully in a non-production environment — an untested backup is not a backup.

## 7. SSL / transport security

- [ ] Valid SSL certificate installed and auto-renewing (e.g. Let's Encrypt).
- [ ] HTTP → HTTPS redirect enforced at the web server or load balancer level.
- [ ] `SESSION_SECURE_COOKIE=true` in `.env` once HTTPS is confirmed working, so session cookies are never sent over plain HTTP.

## 8. Final pre-launch pass

- [ ] `php artisan optimize:clear` run, then `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache` for production performance.
- [ ] Manually click through the primary navigation (home → news → contact → about/plans/privacy/terms) on the deployed URL — confirm no dead links or crashes before announcing the launch.
- [ ] Confirm a non-admin user account (no role, `is_admin = false`, or no account at all) cannot reach `/admin` — should redirect/403, not show the panel.
- [ ] Confirm the real production admin account (Super Admin role) can log into `/admin` successfully and reach طلبات التواصل, المستخدمون, and إعدادات الموقع.
- [ ] Submit a real test message through the live `/contact` form and confirm: the success message shows, the record appears under طلبات التواصل in Filament, and the configured admin mailbox actually receives the notification (not just the log, per §5).
