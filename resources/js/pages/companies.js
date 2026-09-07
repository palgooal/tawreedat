/**
 * Companies directory (`/companies`) — progressively-enhanced pagination
 * and filtering. The page itself is plain server-rendered Blade (no
 * Alpine involvement for the company list): CompanyController::index
 * renders companies.index, which @includes companies/partials/results.blade.php
 * inside #companies-directory-results. Every pagination link and the
 * filter <form> keep their real href/action, so with JS disabled (or a
 * fetch failure) this page works exactly as a normal full-page-reload
 * Laravel pagination flow — nothing here removes that fallback.
 *
 * With JS enabled, this module intercepts those same links/submissions,
 * re-requests the same URL via fetch() with the header Laravel's
 * Request::ajax() checks for, and asks the controller for just the
 * results partial (see the `if ($request->ajax())` branch in
 * CompanyController::index) instead of the full page. The response is
 * swapped into #companies-directory-results, the address bar is updated
 * via history.pushState (so the URL stays bookmarkable/shareable and
 * Back/Forward keep working), and nothing scrolls the viewport — the
 * problem this replaces was exactly that a full navigation always jumps
 * to the top of the page.
 *
 * Separately, the results grid's row height is kept stable across pages
 * by CSS alone (auto-rows-fr on the grid in results.blade.php, plus
 * CompanyController::index's $missingCompanySlots padding out a shorter
 * last page with invisible .company-result-placeholder items) -- no JS
 * measurement is involved. An earlier version of this module measured
 * real cards' rendered height at runtime and applied it to the
 * placeholders, but that made cross-page stability *worse* here: with
 * this data, page 1's 12 real cards span two different natural heights
 * (332px and ~360px), so whichever page happened to be loaded when the
 * measurement ran wasn't a reliable stand-in for the other page's rows.
 * grid-auto-rows: 1fr sidesteps that entirely by letting each page
 * equalize its own rows to its own tallest row, with no DOM measurement.
 */
export function registerCompaniesDirectory() {
    const root = document.getElementById('companies-directory');
    const resultsContainer = document.getElementById('companies-directory-results');

    if (!root || !resultsContainer) {
        // Not on the companies directory page — nothing to wire up.
        return;
    }

    const indexUrl = root.dataset.companiesIndexUrl;
    let indexPathname = null;

    try {
        indexPathname = indexUrl ? new URL(indexUrl, location.href).pathname : null;
    } catch (err) {
        indexPathname = null;
    }

    // Only intercept links that stay on this same route (pagination links,
    // "مسح الفلاتر" / "عرض كل الشركات" — all of which point at the
    // companies.index route with only the query string differing). Any
    // other link inside this region (there aren't any today, but this
    // guards against ones added later, e.g. a link to a single company
    // page) is left to navigate normally.
    function isDirectoryLink(anchor) {
        if (!anchor || !anchor.href) return false;
        if (anchor.target || anchor.hasAttribute('download')) return false;

        let url;
        try {
            url = new URL(anchor.href, location.href);
        } catch (err) {
            return false;
        }

        if (url.origin !== location.origin) return false;
        if (indexPathname && url.pathname !== indexPathname) return false;

        return true;
    }

    // After swapping in new results, move focus somewhere sensible so
    // keyboard/screen-reader users aren't silently dropped back to
    // <body> (the element they had focused, e.g. the pagination link
    // they just activated, no longer exists once innerHTML is replaced).
    // Prefer the new "current page" marker, since that's the most
    // relevant anchor after a pagination click; fall back to the
    // aria-live status line (present after a filter change too). Both
    // are focused with preventScroll — the whole point of this feature
    // is that navigating results must never move the viewport.
    function focusAfterUpdate() {
        const target = resultsContainer.querySelector('[aria-current="page"]')
            || document.getElementById('companies-results-status');

        if (!target) return;

        if (!target.hasAttribute('tabindex')) {
            target.setAttribute('tabindex', '-1');
        }

        target.focus({ preventScroll: true });
    }

    function setBusy(busy) {
        resultsContainer.classList.toggle('opacity-60', busy);
        resultsContainer.classList.toggle('pointer-events-none', busy);
        resultsContainer.setAttribute('aria-busy', busy ? 'true' : 'false');
    }

    async function loadResults(url, { pushState = true } = {}) {
        setBusy(true);

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(`Unexpected response status: ${response.status}`);
            }

            const html = await response.text();
            resultsContainer.innerHTML = html;

            if (pushState) {
                history.pushState({ companiesDirectory: true }, '', url);
            }

            focusAfterUpdate();
        } catch (err) {
            // Network error, non-2xx response, etc. — fall back to a real
            // navigation rather than leaving the page stuck on stale
            // results with no way forward.
            location.href = url;
            return;
        } finally {
            setBusy(false);
        }
    }

    root.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        const anchor = event.target.closest('a');
        if (!anchor || !root.contains(anchor)) return;
        if (!isDirectoryLink(anchor)) return;

        event.preventDefault();
        loadResults(anchor.href);
    });

    root.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!root.contains(form)) return;
        if (form.method.toLowerCase() !== 'get') return;

        event.preventDefault();

        const params = new URLSearchParams(new FormData(form));
        const url = `${form.action.split('?')[0]}?${params.toString()}`;
        loadResults(url);
    });

    // Back/Forward between filter/page states we pushed above. The URL is
    // already correct at this point (the browser changed it for us), so
    // just re-fetch its results without pushing a new history entry.
    window.addEventListener('popstate', () => {
        loadResults(location.href, { pushState: false });
    });
}
