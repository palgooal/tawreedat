# Database Schema — Tawreedat

Last updated: 2026-07-04

Source of truth is always the migrations in `database/migrations/`. This file is a human-readable summary — if it ever disagrees with the migrations, trust the migrations and update this file.

All slug-bearing models generate slugs via `Str::slug($value, '-', null)` (the `null` language argument disables ASCII transliteration, which is required to preserve Arabic characters — the default `Str::slug()` strips them entirely). Slugs are only auto-generated when blank (`static::saving()` hook), so manually-set slugs are respected.

---

## City

**Table:** `cities`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `name` | string | |
| `slug` | string, unique | Auto-generated from `name` if blank |
| `is_active` | boolean | default `true` |
| timestamps | | |

**Relationships:** `hasMany(Company::class)`

---

## Category

**Table:** `categories`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `name` | string | |
| `slug` | string, unique | Auto-generated from `name` if blank |
| `type` | string, nullable | Free-text, not a Filament-enforced enum |
| `is_active` | boolean | default `true` |
| timestamps | | |

**Relationships:** `hasMany(Company::class)`

---

## Company

**Table:** `companies`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `name` | string | |
| `slug` | string, unique | Auto-generated from `name` if blank |
| `logo` | string, nullable | Path on the `public` disk; resolved via `Storage::disk('public')->url()` |
| `description` | text, nullable | |
| `website` | string, nullable | |
| `phone` | string, nullable | |
| `email` | string, nullable | |
| `city_id` | foreignId, nullable | → `cities.id`, `nullOnDelete()` |
| `category_id` | foreignId, nullable | → `categories.id`, `nullOnDelete()` |
| `is_verified` | boolean | default `false` |
| `is_featured` | boolean | default `false` |
| `status` | string | default `pending` |
| timestamps | | |

**Status enum (admin `Select`/filter options):**

| Value | Arabic label |
|---|---|
| `pending` | قيد المراجعة |
| `active` | نشط |
| `inactive` | غير نشط |
| `rejected` | مرفوض |

Only `status = 'active'` companies are shown on the public homepage.

**Relationships:** `belongsTo(City::class)`, `belongsTo(Category::class)`

---

## News

**Table:** `news`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `title` | string | |
| `slug` | string, unique | Auto-generated from `title` if blank |
| `excerpt` | text, nullable | |
| `content` | longText, nullable | |
| `image` | string, nullable | Path on the `public` disk |
| `category` | string, nullable | **Legacy.** Free-text label, not a foreign key. Superseded by `news_category_id` (2026-07-03) but deliberately kept for backward compatibility — see the Phase 2 note in `docs/ROADMAP.md`. New/edited news no longer write to this field via the admin form. |
| `news_category_id` | foreignId, nullable | → `news_categories.id`, `nullOnDelete()`. The real taxonomy relationship going forward. |
| `published_at` | timestamp, nullable | |
| `status` | string | default `draft` |
| timestamps | | |

**Status enum:**

| Value | Arabic label |
|---|---|
| `draft` | مسودة |
| `published` | منشور |

`News::scopePublished()` (added 2026-07-03) encapsulates the visibility rule used everywhere: `status = 'published'` **and** `published_at` is not null **and** `published_at <= now()`. The homepage and public news pages both use this scope rather than duplicating the condition.

**Relationships:** `belongsTo(NewsCategory::class)` — exposed as `categoryRelation()`, not `category()`, because the legacy `category` text attribute already occupies that name on the model. Rename planned once the legacy column is dropped (see `docs/ROADMAP.md`).

---

## NewsCategory

**Table:** `news_categories`

*(Added 2026-07-03, replacing free-text `news.category` with a real taxonomy.)*

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `name` | string | |
| `slug` | string, unique | Auto-generated from `name` if blank |
| `description` | text, nullable | |
| `is_active` | boolean | default `true` |
| timestamps | | |

**Relationships:** `hasMany(News::class)`, exposed as `news()`.

**Data migration:** a dedicated migration (`migrate_news_category_text_to_news_categories`) backfills this table from every distinct existing `news.category` string and points the matching `news.news_category_id` at it. It's a one-way data migration (its `down()` is intentionally a no-op) and only fills `news_category_id` where it's still null, so it's safe to run alongside manually-curated categories (e.g. `NewsCategorySeeder`'s six named categories) without clobbering assignments.

**Public filtering:** `/news?category=` now takes a **slug** (e.g. `?category=asaar-mawad-albinaa`), not free text — matches `NewsCategory.slug` via `whereHas('categoryRelation', ...)`.

---

## AdvertisementSlot

*(Added 2026-07-06 as part of the slot-based advertising refactor — see `docs/DECISIONS.md`.)*

**Table:** `advertisement_slots`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `key` | string, unique | Stable machine key, e.g. `header_banner`. Never shown to admins — used in code (`AdvertisementManager::slot('header_banner')`) and by `Advertisement.advertisement_slot_id`. |
| `name` | string | Arabic display name shown in the admin panel |
| `description` | text, nullable | |
| `width` | integer, nullable | Recommended banner width in px, for admin guidance only — not enforced |
| `height` | integer, nullable | Recommended banner height in px, for admin guidance only — not enforced |
| `is_active` | boolean | default `true`. Deactivating a slot does **not** hide ads already assigned to it — it only signals in the admin panel that the slot itself is retired. |
| timestamps | | |

**Seeded slots** (`AdvertisementSlotSeeder`, also inlined in the creating migration so it's seeded even without running seeders):

| `key` | Arabic `name` | Used by |
|---|---|---|
| `header_banner` | بانر أعلى الموقع | Site-wide header (all pages) **and** reused as the homepage's main banner |
| `home_banner_1` | إعلان الصفحة الرئيسية (1) | Homepage |
| `home_banner_2` | إعلان الصفحة الرئيسية (2) | Homepage |
| `home_banner_3` | إعلان الصفحة الرئيسية (3) | Homepage |
| `news_sidebar` | الشريط الجانبي للأخبار | News article page sidebar |
| `news_footer` | أسفل الخبر | Bottom of news article page |

**Relationships:** `hasMany(Advertisement::class)`, exposed as `advertisements()`.

---

## Advertisement

**Table:** `advertisements`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `title` | string | |
| `image` | string, nullable | Path on the `public` disk |
| `link` | string, nullable | Validated as a URL in the admin form. Frontend never links to this directly — see `ads.click` route below. |
| `position` | string, nullable | **Deprecated 2026-07-06**, kept only for backward compatibility — see `docs/DECISIONS.md`. No longer editable from the admin form; superseded by `advertisement_slot_id`. |
| `advertisement_slot_id` | foreignId, nullable | → `advertisement_slots.id`, `nullOnDelete()`. Added 2026-07-06. The slot this ad is assigned to — this is what the admin form and frontend actually use now. |
| `priority` | integer | default `0`. Added 2026-07-06. When a slot has more than one active ad, the highest-priority one wins (ties broken by newest). |
| `views` | integer | default `0`. Added 2026-07-06. Incremented once per page render that shows this ad (see `AppServiceProvider`'s view composers). |
| `clicks` | integer | default `0`. Added 2026-07-06. Incremented by `AdClickController` when a visitor follows the ad's link. |
| `starts_at` | timestamp, nullable | |
| `ends_at` | timestamp, nullable | |
| `is_active` | boolean | default `true` |
| timestamps | | |

**Legacy position enum** (superseded by slots, column retained only so old data keeps its original value):

| Value | Arabic label |
|---|---|
| `header` | أعلى الصفحة |
| `sidebar` | الشريط الجانبي |
| `footer` | أسفل الصفحة |
| `home` | الصفحة الرئيسية |

**One-time data migration** (`map_advertisement_positions_to_slots`) backfilled `advertisement_slot_id` from the old `position` value: `header → header_banner`, `home → home_banner_1`, `footer → home_banner_3`, `sidebar → news_sidebar`. It only touched rows where `advertisement_slot_id` was still null, so it's safe to re-run and never clobbers a manually-assigned slot. The `position` column itself was **not** dropped and is not currently scheduled for removal.

An ad is eligible to render in its slot only if `is_active = true` **and** the current time falls within `[starts_at, ends_at]` (both bounds optional/nullable — a null bound means unbounded on that side). Among eligible ads in the same slot, `AdvertisementManager::slot()` picks the highest `priority`, then the newest. If no active ad exists for a slot, the relevant view falls back to static placeholder artwork and a `route('contact')` link (homepage) or hides the block entirely (news sidebar/footer).

**Click tracking:** every ad link on the frontend points at `route('ads.click', $advertisement)` (`GET /ad/{advertisement}`) instead of the ad's `link` directly. `AdClickController` increments `clicks` then redirects to `link` (or `route('home')` if `link` is empty).

**Impression tracking:** handled in `AppServiceProvider`'s view composers, not inside `AdvertisementManager::slot()` — the manager's result is cached for 5 minutes, so incrementing there would undercount real page views. `headerBanner` is intentionally resolved twice (site-wide header partial + homepage banner) but only incremented once per request to avoid double-counting.

**CTR:** `Advertisement::getCtrAttribute()` returns `round(clicks / views * 100, 2)`, or `null` (not `0`) when `views` is `0`, so the UI can show "—" instead of a misleading 0%.

**Relationships:** `belongsTo(AdvertisementSlot::class)`, exposed as `slot()`.

---

## ContactRequest

**Table:** `contact_requests`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `name` | string | |
| `email` | string | |
| `phone` | string, nullable | |
| `company` | string, nullable | Free-text, not a foreign key to `Company` |
| `inquiry_type` | string, nullable | Free-text, not a Filament-enforced enum |
| `message` | text, nullable | |
| `status` | string | default `new` |
| timestamps | | |

**Status enum:**

| Value | Arabic label |
|---|---|
| `new` | جديد |
| `in_progress` | قيد المعالجة |
| `resolved` | تم الحل |
| `closed` | مغلق |

**Relationships:** none

**Note:** the public `/contact` page does not currently submit to this table — see `docs/PROJECT_STATUS.md` and `docs/ROADMAP.md`.

---

## CompanyRegistrationRequest

**Table:** `company_registration_requests`

*(Added 2026-07-04 for the "سجّل شركتك" request/review workflow; `city_id`/`category_id`/`logo`/`company_id` added 2026-07-04 in the same-day follow-up that lets an approved request convert into a real `Company`.)*

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `company_name` | string | |
| `contact_name` | string | |
| `phone` | string(50) | |
| `email` | string, nullable | |
| `city` | string(100), nullable | **Legacy/fallback.** A text snapshot of the chosen city's name at submission time, kept for backward compatibility and as a human-readable record even if the `City` row is later renamed/removed. Not a foreign key. |
| `city_id` | foreignId, nullable | → `cities.id`, `nullOnDelete()`. The real relationship — feeds the `Company.city_id` on approval. |
| `category` | string(100), nullable | **Legacy/fallback**, same rationale as `city` above. |
| `category_id` | foreignId, nullable | → `categories.id`, `nullOnDelete()`. The real relationship — feeds `Company.category_id` on approval. |
| `website` | string, nullable | |
| `logo` | string, nullable | Path on the `public` disk, under `company-registration-logos/`. Copied to `Company.logo` on approval (only if present — approving never blanks out an existing Company's logo). |
| `description` | text, nullable | |
| `notes` | text, nullable | Free text from the submitter. |
| `status` | string | default `pending` — see status enum below |
| `reviewed_at` | timestamp, nullable | |
| `reviewed_by` | foreignId, nullable | → `users.id`, `nullOnDelete()` |
| `admin_notes` | text, nullable | Internal, admin-only |
| `company_id` | foreignId, nullable | → `companies.id`, `nullOnDelete()`. Set the first time a request is approved; reused (not re-created) on any subsequent approval of the same request — see `CompanyRegistrationRequestResource::resolveOrCreateCompany()`. |
| timestamps | | |

**Status enum:**

| Value | Arabic label | Creates/updates a Company? |
|---|---|---|
| `pending` | قيد المراجعة | No |
| `approved` | مقبول | **Yes** — see "Approval → Company conversion" below |
| `rejected` | مرفوض | No |
| `contacted` | تم التواصل | No |

**Relationships:** `belongsTo(City::class)` exposed as `cityRelation()`, `belongsTo(Category::class)` exposed as `categoryRelation()` — not `city()`/`category()`, because the legacy text columns of the same name already occupy those attribute names on the model (identical situation, and identical fix, to `News::categoryRelation()` above). Also `belongsTo(Company::class)` (`company()`), `belongsTo(User::class, 'reviewed_by')` (`reviewedBy()`).

**Approval → Company conversion (added 2026-07-04):** clicking "قبول" in `CompanyRegistrationRequestResource` creates a `Company` (or updates the one already linked via `company_id`) with `name`/`description`/`website`/`phone`/`email`/`city_id`/`category_id`/`logo` copied over. A brand-new `Company` gets `is_verified = false`, `is_featured = false`, `status = 'active'` — those three are **not** reset on an update, since they're admin-curated flags that live on the Company record itself. Once `status = 'active'`, the Company is picked up automatically by `HomeController`'s existing `where('status', 'active')` query — no separate "publish" step. Rejecting or marking a request as contacted never touches `companies`. See `docs/DECISIONS.md`.

---

## Entity-relationship summary

```
City       1 ──< Company >── 1  Category
City       1 ──< CompanyRegistrationRequest >── 1  Category
Company    1 ──< CompanyRegistrationRequest  (company_id, nullable, nullOnDelete)
User       1 ──< CompanyRegistrationRequest  (reviewed_by, nullable, nullOnDelete)
NewsCategory 1 ──< News   (news_category_id, nullable, nullOnDelete)
AdvertisementSlot 1 ──< Advertisement  (advertisement_slot_id, nullable, nullOnDelete)
ContactRequest        (no FKs)
Page                  (no FKs)
```

`Company` (`city_id`, `category_id`), `CompanyRegistrationRequest` (`city_id`, `category_id`, `company_id`, `reviewed_by`), and `News` (`news_category_id`) foreign keys are all nullable with `nullOnDelete()`, so removing a city/category/news-category/company/user does not cascade-delete the records that reference it — they just become uncategorized/unlinked.
