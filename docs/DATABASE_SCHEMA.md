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

## Advertisement

**Table:** `advertisements`

| Field | Type | Notes |
|---|---|---|
| `id` | id | |
| `title` | string | |
| `image` | string, nullable | Path on the `public` disk |
| `link` | string, nullable | Validated as a URL in the admin form |
| `position` | string, nullable | See enum below |
| `starts_at` | timestamp, nullable | |
| `ends_at` | timestamp, nullable | |
| `is_active` | boolean | default `true` |
| timestamps | | |

**Position enum (admin `Select`/filter options):**

| Value | Arabic label | Used by homepage? |
|---|---|---|
| `header` | أعلى الصفحة | Yes — first ad card |
| `sidebar` | الشريط الجانبي | Yes — second ad card |
| `footer` | أسفل الصفحة | Not currently consumed by any view |
| `home` | الصفحة الرئيسية | Yes — main banner ad |

The homepage only shows an ad for a given position if it is `is_active = true` **and** the current time falls within `[starts_at, ends_at]` (both bounds optional/nullable — a null bound means unbounded on that side). If no active ad exists for a slot, the homepage falls back to static placeholder artwork and a `route('contact')` link.

**Relationships:** none

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
Advertisement        (no FKs)
ContactRequest        (no FKs)
Page                  (no FKs)
```

`Company` (`city_id`, `category_id`), `CompanyRegistrationRequest` (`city_id`, `category_id`, `company_id`, `reviewed_by`), and `News` (`news_category_id`) foreign keys are all nullable with `nullOnDelete()`, so removing a city/category/news-category/company/user does not cascade-delete the records that reference it — they just become uncategorized/unlinked.
