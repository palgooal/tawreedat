# Product

## Register

brand

## Users

Two groups meet on Tawreedat (توريد): construction-materials factories, contractors, and suppliers across Saudi Arabia who want to be found and trusted, and the buyers/procurement people searching for them by city or activity type. Visitors arrive expecting an Arabic-first (RTL), credible, national-scale industrial directory — not a consumer marketplace. The primary on-page task is search-first: find the right supplier or contractor fast, or submit a company/contact request to be listed.

## Product Purpose

Tawreedat is "دليل مصانع مواد البناء بالمملكة العربية السعودية" — a directory/guide of building-materials factories, contractors, and suppliers in Saudi Arabia. v1 is deliberately not a full company-profile browsing product: it exists to introduce the platform, publish news/advertisements, attract organic traffic, build brand identity and trust, and capture contact/registration leads ("سجّل شركتك") for manual, human-reviewed follow-up. A simple MVP Companies Directory (search + city/category/verified/featured filters) shipped 2026-07-06; full per-company profile pages are Phase 2. Success looks like: visitors trust the platform enough to search it and companies trust it enough to register.

## Brand Personality

Trustworthy, official, professional. The Gov Green + Gold palette and the government/sector-logo strip on the public site carry institutional credibility — the platform should feel like a dependable, national-scale reference, not a startup experimenting with its identity.

## Anti-references

- **Generic AI SaaS template** — no cream/sand near-white body backgrounds, no gradient text, no hero-metric-card clichés, no tiny uppercase eyebrow labels above every section.
- **Dull bureaucratic government site** — official and professional must not mean stiff, dated, or lifeless; credibility should coexist with a site that's pleasant to actually use.
- **Flashy consumer marketplace** — this is a B2B industrial directory, not an OLX-style classifieds site; avoid marketplace clutter, loud promotional treatments, or consumer-app playfulness.

## Design Principles

1. **Credibility over cleverness.** Every design decision should reinforce trust for a national industrial directory first; novelty and trend-chasing are secondary at best.
2. **Institutional warmth.** Official and professional (Gov Green / Gold, government-logo credibility) without tipping into cold or bureaucratic.
3. **Search-first utility.** The hero and directory experience exist to get buyers to the right supplier fast — brand storytelling supports that task, it never slows it down.
4. **RTL as the default, not an afterthought.** Arabic-first layout, typography, and interaction patterns throughout; there is no LTR variant to fall back on.
5. **Restraint over flash.** A B2B industrial audience calls for restraint — avoid both consumer-marketplace loudness and generic AI-SaaS scaffolding.

## Accessibility & Inclusion

Target: WCAG 2.1 AA. Build on the practices already established in the codebase rather than reinventing them: RTL-first layout, paired `sr-only` labels on icon-only or visually-implicit inputs, `aria-hidden`/`tabindex="-1"` on conditionally-visible interactive elements, global `prefers-reduced-motion` support, and zero `href="#"` usage (every link is a real route, anchor, or button).
