---
name: Tawreedat
description: دليل مصانع مواد البناء بالمملكة العربية السعودية — a trustworthy, official directory of building-materials factories and suppliers.
colors:
  official-green: "#17643f"
  official-green-hover: "#144f34"
  official-green-active: "#12412b"
  official-green-tint: "#eefbf3"
  ink-green: "#012c26"
  ink-green-bright: "#014236"
  seal-gold: "#e6ac2e"
  seal-gold-deep: "#d99416"
  seal-gold-tint: "#fdf8ec"
  paper: "#f8fafc"
  ink: "#1e293b"
  border-quiet: "#e2e8f0"
  border-input: "#cbd5e1"
typography:
  display:
    fontFamily: "Alexandria, ui-sans-serif, system-ui, 'Segoe UI', Tahoma, Arial, sans-serif"
    fontSize: "clamp(2.5rem, 6vw, 4.5rem)"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "normal"
  headline:
    fontFamily: "Alexandria, ui-sans-serif, system-ui, 'Segoe UI', Tahoma, Arial, sans-serif"
    fontSize: "clamp(1.875rem, 3vw, 2.25rem)"
    fontWeight: 800
    lineHeight: 1.25
  title:
    fontFamily: "Alexandria, ui-sans-serif, system-ui, 'Segoe UI', Tahoma, Arial, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 800
    lineHeight: 1.3
  body:
    fontFamily: "Alexandria, ui-sans-serif, system-ui, 'Segoe UI', Tahoma, Arial, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.7
  label:
    fontFamily: "Alexandria, ui-sans-serif, system-ui, 'Segoe UI', Tahoma, Arial, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    letterSpacing: "normal"
rounded:
  button: "8px"
  card: "16px"
  pill: "9999px"
  hero: "24px"
spacing:
  xs: "8px"
  sm: "12px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.official-green}"
    textColor: "#ffffff"
    rounded: "{rounded.button}"
    padding: "10px 16px"
  button-primary-hover:
    backgroundColor: "{colors.official-green-hover}"
  button-primary-active:
    backgroundColor: "{colors.official-green-active}"
  button-secondary:
    backgroundColor: "#ffffff"
    textColor: "{colors.ink}"
    rounded: "{rounded.button}"
    padding: "10px 16px"
  card:
    backgroundColor: "#ffffff"
    rounded: "{rounded.card}"
    padding: "16px"
---

# Design System: Tawreedat

## 1. Overview

**Creative North Star: "The National Registry"**

Tawreedat (توريد) reads like an official registry, not a startup landing page: a green-and-gold identity borrowed from government and institutional credibility, applied to a search-first industrial directory. Every surface should feel like it belongs to a national reference — precise, verifiable, calm — while staying warm enough that a factory owner or buyer wants to actually use it, not just trust it from a distance. Ink Green (near-black) carries the site's authority in the header and hero chrome; Official Green carries action; Seal Gold is rationed almost entirely to verified/featured credibility markers, so when it appears, it means something.

This system explicitly rejects three failure modes: the generic AI-SaaS look (cream/sand backgrounds, gradient text, hero-metric cards, uppercase eyebrow labels above every section), the dull bureaucratic government site (stiff, dated, lifeless despite the institutional palette), and the flashy consumer marketplace (OLX-style clutter, loud promotional treatments). Restraint is the discipline that keeps "official" from curdling into either extreme.

**Key Characteristics:**
- RTL-first Arabic layout — not a toggle, the only mode.
- Deep green + gold read as institutional credibility, not corporate tech.
- Gold is rare and earned — it marks verification, not decoration.
- Mostly flat surfaces; elevation escalates only for hero-scale moments.
- Search is the hero, not the pitch — the directory's job is to get buyers to a supplier fast.

## 2. Colors

The palette is a green-and-gold institutional system: deep green for authority and action, gold rationed to credibility signals, warm-neutral slate for everything that needs to stay quiet.

### Primary
- **Official Green** (#17643f): the primary action color — buttons, links, focus rings, active nav states. Darkens to #144f34 on hover and #12412b on active/press.
- **Ink Green** (#012c26): the deep, near-black chrome of the sticky nav, main header, and hero gradient (paired with the brighter interior stop #014236). This is where the "official registry" authority lives — never used for body text or small UI, only large chrome surfaces.

### Secondary
- **Seal Gold** (#e6ac2e / deep #d99416): reserved almost entirely for verified/featured badges and small credibility highlights on the Companies Directory. It is not a general decorative accent — its scarcity is what makes a gold badge mean "verified" rather than "pretty."

### Neutral
- **Paper** (#f8fafc): the page background (`bg-slate-50`) — a true near-white, not a warm cream/sand tint. Keeps the institutional palette from drifting into "AI-SaaS warm neutral."
- **Ink** (#1e293b): primary body text (`text-slate-800`) — deliberately dark enough to clear 4.5:1 contrast; no light-gray-for-elegance body copy anywhere.
- **Border Quiet** (#e2e8f0): the standard card/section border (`border-slate-200`), used at `card-soft` and section-dividing weight.
- **Border Input** (#cbd5e1): secondary-button and form-input borders (`border-slate-300`), one step darker than Border Quiet for interactive elements.

### Named Rules
**The Rationed Gold Rule.** Seal Gold appears only where verification or featured status is real — a badge, a highlight tied to an actual credibility signal. It never fills a background, a button, or a decorative accent; the moment gold shows up everywhere, it stops meaning anything.

**The No-Cream Rule.** The body background is Paper (#f8fafc), a true near-white — never a warm cream/sand/parchment tint. Warmth in this brand comes from the green-and-gold identity and Alexandria's letterforms, not from a tinted body background.

## 3. Typography

**Display / Body / Label Font:** Alexandria (with a full system-font fallback stack: ui-sans-serif, system-ui, Segoe UI, Tahoma, Arial). One family across the entire platform, including the Filament admin panel, for a single consistent identity.

**Character:** A single geometric-leaning Arabic-first sans carries every role, from the 72px hero mark down to badge labels — hierarchy comes from weight and size, not from mixing families. This keeps the "official registry" register: one voice, used with discipline, not a decorative pairing.

### Hierarchy
- **Display** (800 extrabold, `clamp(2.5rem, 6vw, 4.5rem)`, line-height 1.1): the "توريد" hero wordmark and homepage H1 — used once per page, at most.
- **Headline** (800 extrabold, ~30–36px, line-height 1.25): section titles like "مساحات إعلانية" — one per major homepage section.
- **Title** (800 extrabold, 20px, line-height 1.3): sub-section headings like "آخر الأخبار", card group titles.
- **Body** (400 regular, 14px, line-height 1.7): paragraph copy, descriptions, form helper text. Cap prose measure at 65–75ch.
- **Label** (600 semibold, 12px): badges, nav items, filter chips, `sr-only` field labels — small text that still needs to read as confident, not throwaway.

### Named Rules
**The One-Voice Rule.** Alexandria is the only typeface, everywhere — public site and admin panel alike. Hierarchy is built with weight (400 → 600 → 800) and size, never with a second family.

## 4. Elevation

Elevation is restrained by default and dramatic only at hero scale. Ordinary cards, buttons, and inputs sit nearly flat (`shadow-sm` or no shadow at all) — depth here is mostly conveyed through Border Quiet, not shadow. Shadow is earned, not ambient: it shows up at `shadow-2xl` only on full-bleed, hero-level surfaces (the homepage hero background, the main ad banner) where the extra weight signals genuine visual importance rather than routine card styling.

### Shadow Vocabulary
- **Resting** (`box-shadow: none` or `0 1px 2px rgba(0,0,0,0.05)` / `shadow-sm`): the default for `card-soft`, buttons, inputs, and everyday list items.
- **Hero** (`shadow-2xl`): reserved for the homepage hero background and the main advertising banner — the only surfaces allowed this much weight.

### Named Rules
**The Earned-Shadow Rule.** A card does not get more shadow because it wants to feel important. Only genuinely hero-scale, full-bleed surfaces graduate from `shadow-sm` to `shadow-2xl`; everything in between stays flat.

## 5. Components

### Buttons
- **Shape:** 8px radius (`rounded-lg`) — smaller than the card radius, so buttons read as precise controls, not soft blobs.
- **Primary:** Official Green (#17643f) background, white text, 10px/16px padding; darkens through hover (#144f34) and active (#12412b); a visible focus-visible ring in Official Green at 2px offset.
- **Secondary:** white background, Ink (#1e293b) text, Border Input (#cbd5e1) outline; hovers to Paper (#f8fafc), presses to `slate-100`. Same focus-ring treatment as primary.
- **Disabled:** 50% opacity, pointer-events removed — no separate disabled palette.

### Badges (verified / featured)
- **Style:** small, `rounded-full` pill, Seal Gold tint background (#fdf8ec) with Seal Gold Deep (#d99416) text/icon — the only place gold fills a surface, and only at low-saturation tint strength.
- **Rule:** a badge only ever represents a real verified/featured flag on the record; never decorative.

### Cards / Containers
- **Corner style:** 16px radius (`rounded-2xl`) is the practical ceiling for ordinary cards, forms, and list items.
- **Background:** white, with a Border Quiet (#e2e8f0) 1px border — this is `card-soft`.
- **Shadow strategy:** resting shadow only (see Elevation) — cards never reach for `shadow-2xl`.
- **Hero / feature exception:** large hero and ad-banner blocks may use up to 24px radius (`rounded-3xl`) and `shadow-2xl` — deliberately more decorative, and deliberately rare.

### Inputs / Fields
- **Style:** white background, Border Input (#cbd5e1) stroke, `rounded-lg`/`rounded-2xl` depending on context (hero search uses the larger radius to match its card).
- **Focus:** Official Green focus-visible ring, 2px, 2px offset — matches the button focus treatment for one consistent interaction language.
- **Labels:** every icon-only or visually-implicit input pairs with a real `sr-only` label — never a placeholder standing in for a label.

### Navigation
- **Style:** a single sticky nav (`#site-sticky-nav`), white/95% with backdrop blur and a Border Quiet bottom edge — not a full sticky header. A compact brand mark and CTA fade in inside that same nav once the page scrolls past the full header.
- **Typography:** Label-weight (600 semibold, small size) nav items; active/hover states shift toward Official Green.
- **Mobile:** the same single sticky nav collapses to its compact state; no separate mobile-only header component.

## 6. Do's and Don'ts

### Do:
- **Do** keep the body background Paper (#f8fafc) — a true near-white, never a warm cream/sand tint.
- **Do** ration Seal Gold to real verified/featured signals only; keep it off buttons, backgrounds, and general decoration.
- **Do** cap ordinary cards, buttons, and inputs at `rounded-2xl` (16px); reserve `rounded-3xl`/24px+ for hero and ad-banner blocks only.
- **Do** keep body text at Ink (#1e293b) or darker — verify 4.5:1 contrast on every background, including tinted ones.
- **Do** use Alexandria at every weight/size instead of introducing a second typeface anywhere, including the admin panel.
- **Do** respect `prefers-reduced-motion` globally and pair every icon-only input with a real `sr-only` label.

### Don't:
- **Don't** use a generic AI-SaaS template look: no gradient text, no hero-metric-card clichés, no tiny uppercase tracked eyebrow label above every section.
- **Don't** let the institutional palette read as a dull bureaucratic government site — official and professional is not an excuse for stiff, dated, or lifeless UI.
- **Don't** design toward a flashy consumer marketplace: no OLX-style clutter, no loud promotional banners competing with the hero, no marketplace-style badge spam.
- **Don't** use `border-left`/`border-right` colored stripes as a card or list-item accent.
- **Don't** reach for `shadow-2xl` on ordinary cards — it's reserved for hero-scale, full-bleed surfaces only.
- **Don't** build an LTR variant or treat RTL as optional — `direction: rtl` is the only mode this site has.
