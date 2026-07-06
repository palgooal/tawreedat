# Roadmap — Tawreedat

Last updated: 2026-07-06

Cross-reference `docs/PROJECT_STATUS.md` for current checkbox state — this file is the plan, that file is the tracker.

## v1 scope (product decision, 2026-07-03)

The first version of the Tawreedat platform is deliberately **not** a full company-profile browsing product. v1 focuses on: introducing the platform, news and advertisements, attracting organic traffic, building brand identity/trust, and capturing contact/join requests. Full company-profile browsing is out of v1 scope by design — see `docs/DECISIONS.md`. This originally reprioritized both Companies Directory and Company Profile out of Phase 1 and into Phase 2 below.

**Update, 2026-07-06:** the Companies Directory half of that Phase 2 item shipped as a simple MVP listing page (`/companies`) — search, city/category/verified/featured filters, pagination, contact info shown directly on each card. Company Profile (`/companies/{slug}`) remains deferred; the directory intentionally does not link anywhere per-company. See "Companies Directory" in `docs/PROJECT_STATUS.md` and the corresponding entry in `docs/DECISIONS.md`.

## Phase 1 — Complete the core public site (v1 scope)

- [x] Finish homepage (DB-backed categories/companies/news/ads, empty states, fallback images, real counts)
- [ ] About — review and finalize content/design (platform introduction/trust-building)
- [ ] Plans — review and finalize content/design. **Reprioritized 2026-07-04:** Tawreedat MVP will not use fixed pricing plans or online payment (see "Company Registration Requests" below and `docs/DECISIONS.md`), so this page's original purpose is gone; either repurpose `/plans` for something else or remove it from the nav entirely — it's currently unlinked from any CTA but the route/view still exist.
- [x] **COMPLETED 2026-07-04** — Company Registration Requests ("سجّل شركتك"): replaced the old `/plans`-linked CTA with `/register-company`, a request/review workflow (`CompanyRegistrationRequest`, status `pending` → `approved`/`rejected`/`contacted`) with city/category dropdowns and an optional logo upload. Same honeypot + `throttle:5,1` protection and admin-notification pattern as the contact form. Approving a request now creates/updates a real `Company` record (same-day follow-up), so it appears on the homepage automatically. See `docs/PROJECT_STATUS.md` → "Company Registration Requests" and `docs/DECISIONS.md`. **Explicitly not built:** online payment, subscription/pricing tables, a public company-profile page, or company-owner accounts.
- [x] **COMPLETED 2026-07-03** — Connect Contact form submissions to the `ContactRequest` model. `ContactController@store` persists every valid submission, notifies admins, and is protected by a honeypot and `throttle:5,1` rate limiting. Verified end-to-end via a live browser test (valid submission, honeypot false-submission, rate-limit trigger). See `docs/PROJECT_STATUS.md` → "Contact Form & Spam Protection".
- [x] **COMPLETED 2026-07-03** — Connect News (`/news`, `/news/{slug}`) to the database. `NewsController` queries `News`/`NewsCategory` live (search, category filter, pagination, related news), replacing the old static/demo Alpine data. Includes the News Categories system — see `docs/PROJECT_STATUS.md` → "News".
- [x] **COMPLETED 2026-07-06** — Homepage entry points that target `companies.index` (hero "تصفح الشركات" button, hero search) now work: the button was un-commented (it was already fully built/styled, just never rendered), and `/companies` is a real, working directory. The dead `openCompany()` Alpine method — which built a URL to the still-unbuilt `companies.show` route but was never wired to any `@click` — was removed rather than fixed, since company cards intentionally don't link anywhere per-company yet.

## Phase 2 — Company Profile (Companies Directory shipped early, see below)

- [x] **COMPLETED 2026-07-06** — Companies Directory (`/companies`) — built `resources/views/companies/index.blade.php` backed by `CompanyController@index`: search (name/description/phone/email/website/city/category), city/category filters by slug, verified/featured filters, featured→verified→newest sort, 12/page pagination with query-string preservation. Matches the query params the homepage already sent via `route('companies.index')`. Shipped as an MVP simple directory rather than waiting for the full Phase 2 batch, since it didn't depend on Company Profile or auth.
- [ ] Company Profile (`/companies/{slug}`) — still not built. Needs the view, a real `{slug}` route parameter (current `companies.show` route is a fixed `/companies/sample` placeholder with no parameter), and a controller. The new directory's cards deliberately show contact info (phone/email/website) directly instead of linking to a profile, so this remains a clean, non-urgent follow-up rather than a broken link.
- [x] **COMPLETED 2026-07-04** — Approving a `CompanyRegistrationRequest` now creates/updates a real `Company` record (`CompanyRegistrationRequestResource::approve()`), so it appears in the homepage's active-companies section automatically, and — as of 2026-07-06 — in the Companies Directory too, once `status = active`. What's still missing for Phase 2: a dedicated public company-profile page — a `Company` with `status = active` today still has no page of its own to link to. See `docs/DATABASE_SCHEMA.md` and `docs/DECISIONS.md`.
- [ ] Authentication — public-facing auth if/when needed (e.g. company owners managing their own listing); currently only the Filament admin has login
- [x] **COMPLETED 2026-07-03** (landed ahead of Phase 2) — Admin permissions: roles/policies via `spatie/laravel-permission` so not every authenticated admin user has unrestricted access to every resource. 4 roles (Super Admin, Admin, Editor, Support), 15 permissions (13 original + 2 for registration requests, added 2026-07-04). All resources except `CategoryResource`/`CityResource` (deliberately left ungated — low-risk reference data, see `docs/ADMIN_PANEL.md`) are permission-gated. See `docs/ADMIN_PANEL.md` → "Roles & Permissions" / "User Management".

## Phase 3 — Production readiness

- [x] **COMPLETED 2026-07-03** — SEO: Site Settings-driven meta tags, Open Graph/Twitter tags, canonical URLs, and per-page overrides for all v1 pages (home, news, contact, static pages). The Companies Directory (added 2026-07-06) got its own `$seoTitle`/`$seoDescription` too. Structured data (`Organization`/`LocalBusiness` per company) remains pending until Company Profile ships — there's still no per-company page to mark up.
- [x] **COMPLETED 2026-07-03** — Sitemaps: `/sitemap.xml` covers home, news index, contact, published static pages, and every published news article. `/companies` itself is not yet added to the sitemap (worth a small follow-up now that it's a real, indexable page); per-company URLs remain excluded since Company Profile doesn't exist yet.
- [ ] Performance — image optimization/CDN for uploaded logos and news images, query/N+1 audit as data volume grows
- [ ] Deployment — production server, domain, SSL, queue workers, backups (see `docs/PROJECT_STATUS.md` → Deployment)

## Future

- [ ] Remove the legacy `news.category` text column. The News Categories system (2026-07-03) introduced a real `NewsCategory` model/table and `news.news_category_id` FK, replacing text-based categorization. The old `category` column was deliberately left in place for backward compatibility during the transition (a data migration backfills `news_category_id` from it automatically). Once production news data has been migrated and verified against the new `news_categories` taxonomy, drop the `category` column via a new migration, remove it from `News::$fillable`, and rename `News::categoryRelation()` to `News::category()`. Check for any remaining reads of `$news->category` (as opposed to `$news->categoryRelation`) before dropping.
- [ ] Remove the transitional `is_admin` field once every real admin account is confirmed to have a role assigned and nothing checks `is_admin` for panel entry anymore. Search the codebase for `is_admin` before doing so. See `docs/DECISIONS.md` and `docs/ADMIN_PANEL.md` → "is_admin is transitional".

## Notes for whoever picks this up next

- Company Profile is **still intentionally** deferred to Phase 2 — this is a product-scope decision (v1 is about identity/trust/traffic/contact-capture, not full company browsing), not a technical gap to rush. Don't build `/companies/{slug}` ahead of schedule without confirming the scope decision has changed. The Companies Directory itself, by contrast, shipped early (2026-07-06) as a simple MVP since it stood on its own without needing profiles or auth.
- All homepage entry points that target `companies.index` (hero CTA, hero search) now work correctly — this loose end from the earlier deferral is closed.
- Read `docs/DECISIONS.md` before changing slug generation, Filament theming, the admin panel shell, or the v1 scope itself — several non-obvious constraints (Arabic slugs, "no vendor edits," RTL being locale-driven, Company Profile still deferred) are easy to accidentally regress.
