import './bootstrap';
import { registerHomeApp } from './pages/home';
import { registerPartnerLogosCarousel } from './components/partner-logos-carousel';
import { registerCompaniesDirectory } from './pages/companies';

// Alpine.js itself is loaded from a CDN <script defer> tag in
// layouts/app.blade.php, positioned AFTER this bundle so that any
// Alpine.data(...) registration made from here runs before Alpine's own
// script calls Alpine.start() (which is what actually processes `x-data`
// attributes in the DOM and fires the `alpine:init` event). Registering a
// component after that point would be too late — this is why that script
// tag's position in the layout matters and must stay after @vite(...).
//
// Each page opts in by setting its own `window.<page>Config` global before
// this listener runs (via a small inline script in that page's Blade view,
// e.g. window.tawreedatHomeConfig on the homepage) — pages that don't set
// it simply skip that page's registration.
document.addEventListener('alpine:init', () => {
    if (window.tawreedatHomeConfig) {
        registerHomeApp(window.tawreedatHomeConfig);
    }

    // Generic, parameterized component (x-data="partnerLogosCarousel(...)")
    // — it takes its data as a call argument rather than reading a global,
    // so it doesn't depend on window.tawreedatHomeConfig and is registered
    // unconditionally, exactly like Alpine.data('app', ...) would be for
    // any other page that reused it.
    registerPartnerLogosCarousel();
});

// Not Alpine-driven at all (the companies directory is plain server-
// rendered Blade — see resources/js/pages/companies.js), so it doesn't
// need to wait for alpine:init. It no-ops on every page except
// /companies, where it finds #companies-directory in the DOM.
registerCompaniesDirectory();
