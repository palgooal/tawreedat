# Design System — Tawreedat

Last updated: 2026-07-03

Tailwind v4, CSS-first config. Source of truth: `resources/css/app.css` (public site) and `resources/css/filament/admin/theme.css` (admin panel — separate identity, documented in `docs/ADMIN_PANEL.md`).

## Typography

- **Primary typeface:** Alexandria, loaded via `<link>` preconnect/stylesheet tags in `layouts/app.blade.php` (not `@import` in CSS, to avoid a render-blocking double load).
- Registered in Tailwind as `--font-sans`, with a full system-font fallback stack (`ui-sans-serif, system-ui, 'Segoe UI', Tahoma, Arial, sans-serif, ...`).
- Also used as the Filament admin panel font (`->font('Alexandria')`) for a consistent identity across public site and admin.

## Colors

### Gov Green palette (`--color-gov-*`) — primary brand color

| Token | Hex |
|---|---|
| `gov-50` | `#eefbf3` |
| `gov-100` | `#d5f5e1` |
| `gov-200` | `#ade8c8` |
| `gov-300` | `#7ad4a8` |
| `gov-400` | `#4ab884` |
| `gov-500` | `#2b9c68` |
| `gov-600` | `#1d7d52` |
| `gov-700` | `#17643f` |
| `gov-800` | `#144f34` |
| `gov-900` | `#12412b` |

`gov-950` also appears in templates (hero/ad section backgrounds) as a near-black-green — it is used via arbitrary values / inline gradients rather than being a declared `@theme` token; if it becomes a recurring need, promote it to a real `--color-gov-950` token.

### Gold palette (`--color-gold-*`) — accent color

| Token | Hex |
|---|---|
| `gold-50` | `#fdf8ec` |
| `gold-100` | `#faedc4` |
| `gold-200` | `#f5db8d` |
| `gold-300` | `#eec455` |
| `gold-400` | `#e6ac2e` |
| `gold-500` | `#d99416` |
| `gold-600` | `#b8730f` |
| `gold-700` | `#925810` |

### Admin-only palette (Filament theme, not shared with the public site)

`--tawreedat-green: #063f34`, `--tawreedat-green-dark: #052e26`, `--tawreedat-gold: #d4a017`, `--tawreedat-cream: #fbfaf7`. These are close to but distinct from the public Gov/Gold palette — the admin panel intentionally has its own deeper, more formal identity. Do not assume the two palettes are interchangeable.

## Components (`@layer components` in `resources/css/app.css`)

Currently defined as reusable classes:

- **`.container-page`** — `mx-auto w-full max-w-[1500px] px-4 sm:px-6 lg:px-8`. Standard page-width wrapper used across sections.
- **`.card-soft`** — `rounded-2xl border border-slate-200 bg-white shadow-sm`. Base card treatment.
- **`.btn-primary`** — solid `gov-700` button with hover/active/focus-visible/disabled states.
- **`.btn-secondary`** — outlined white button with the same state coverage.

**Not yet extracted as formal component classes:** `page-hero` and `section-title` are used as *patterns* (recurring combinations of utility classes for hero sections and section headings — see `pages/home.blade.php` for the current inline implementation) but are not defined in `@layer components`. If consistency across more pages becomes a problem, extracting them into named classes is a good next step — see `docs/ROADMAP.md`.

## Rules

- **RTL-first.** `html { direction: rtl; }` is set globally in `@layer base`. There is no LTR variant of the site; RTL is not a toggle, it's the default.
- **Accessible labels.** Icon-only or visually-implicit inputs get a paired `sr-only` `<label>` (see hero search/city select, filter controls in `pages/home.blade.php`). Interactive elements that are only conditionally meaningful use `:aria-hidden` and `:tabindex="-1"` when hidden (see the sticky-nav brand/CTA fade-in).
- **No `href="#"`.** Confirmed zero occurrences across `resources/views`. Every link either uses `route()`, a real anchor target, or a JS-driven `@click` handler on a `<button>`.
- **Sticky nav standard:** exactly one sticky element — `<nav id="site-sticky-nav" class="sticky top-0 z-50 ...">` — not a sticky full header. Alpine tracks scroll position via `navStuck` (compared against the nav's `offsetTop`) and uses it to fade in a compact brand mark + CTA inside that same nav once the page has scrolled past it. There is no separate "sticky header" component.
- **Rounded corners: `rounded-2xl` is the practical maximum** for cards and standard UI surfaces (`card-soft`, buttons, inputs). Larger radii (`rounded-3xl`, `rounded-[36px]`) appear only on large hero/ad feature blocks, which are deliberately more decorative — don't use those bigger radii on ordinary cards, forms, or list items.
- **Avoid `shadow-2xl` unless necessary.** Reserve it for hero-level or full-bleed feature blocks (e.g. the homepage hero background, the main ad banner). Everyday cards use `shadow-sm` (`card-soft`) or no shadow at all.
- **Reduced motion is respected globally** — `@media (prefers-reduced-motion: reduce)` collapses all animation/transition durations to near-zero.
- **`[x-cloak]`** is hidden via CSS by default to prevent a flash of uninitialized Alpine content — always add `x-cloak` to elements whose initial visibility depends on Alpine state.
