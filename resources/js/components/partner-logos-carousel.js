/**
 * Partner Logos carousel — infinite-loop, autoplay, drag/swipe, dot
 * pagination. Modeled on logocarousel.com/carousel's "Custom Margin &
 * GrayScale on hover Normal" demo — see the CSS comment in resources/css/app.css
 * (`.logos-carousel__*`) for the visual/measurement reference this pairs with.
 *
 * Self-contained, generic Alpine component: it takes the partner-logo list
 * as a constructor argument (`x-data="partnerLogosCarousel(partnerLogos)"`)
 * rather than reading any global — it has no dependency on the homepage's
 * config bridge or on resources/js/pages/home.js, and contains no Blade
 * syntax. `partnerLogos` itself still comes from the existing homepage data
 * source (window.tawreedatHomeConfig, via the `app` component's own
 * `partnerLogos` state) — this file only receives it as a parameter, it
 * never fetches, duplicates, or hardcodes it.
 */
export function registerPartnerLogosCarousel() {
    Alpine.data('partnerLogosCarousel', (partnerLogos) => ({
        logosPerView: 5,
        logosClones: 5,
        logosIndex: 0,
        logosCardWidth: 200,
        logosStep: 220,
        logosInstant: false,
        logosTimer: null,
        logosBoundaryTimer: null,
        logosDragging: false,
        logosDidDrag: false,
        logosDragStartX: 0,
        logosDragBaseIndex: 0,
        logosDragOffsetPx: 0,
        get logosExtended() {
            const n = this.logosClones;
            return [
                ...partnerLogos.slice(-n).map((l, i) => ({ ...l, key: `pre-${i}` })),
                ...partnerLogos.map((l, i) => ({ ...l, key: `real-${i}` })),
                ...partnerLogos.slice(0, n).map((l, i) => ({ ...l, key: `post-${i}` })),
            ];
        },
        get logosActiveDot() {
            const len = partnerLogos.length;
            return ((this.logosIndex % len) + len) % len;
        },
        get logosTrackStyle() {
            const offset = -(this.logosClones + this.logosIndex) * this.logosStep + this.logosDragOffsetPx;
            // Also carries --logos-card-width, a CSS custom property consumed
            // by .logos-carousel__card (resources/css/app.css) — set once
            // here on the track instead of per-card, since custom properties
            // inherit to every descendant card for free. This keeps the
            // track down to a single :style binding rather than adding a
            // second one alongside it.
            return `transform: translateX(${offset}px); --logos-card-width: ${this.logosCardWidth}px`;
        },
        init() {
            this.setLogosPerView();
            // Stored (not inline-anonymous) so destroy() can remove exactly
            // this listener — same behavior as before, just cleanly
            // reversible if Alpine ever tears this component down.
            this._onResize = () => this.setLogosPerView();
            window.addEventListener('resize', this._onResize);
            this.restartLogosAutoplay();
        },
        destroy() {
            window.removeEventListener('resize', this._onResize);
            clearInterval(this.logosTimer);
            clearTimeout(this.logosBoundaryTimer);
        },
        setLogosPerView() {
            const w = window.innerWidth;
            this.logosPerView = w < 736 ? 2 : w < 980 ? 3 : w < 1200 ? 4 : 5;
            this.$nextTick(() => this.measureLogosStep());
        },
        measureLogosStep() {
            const vp = this.$refs.logosViewport;
            if (!vp) return;
            const gap = 20;
            const width = vp.getBoundingClientRect().width;
            this.logosCardWidth = (width - gap * (this.logosPerView - 1)) / this.logosPerView;
            this.logosStep = this.logosCardWidth + gap;
        },
        goToLogo(i) {
            this.logosIndex = i;
            this.restartLogosAutoplay();
        },
        restartLogosAutoplay() {
            clearInterval(this.logosTimer);
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (partnerLogos.length <= 1) return;
            this.logosTimer = setInterval(() => {
                this.logosIndex++;
                this.scheduleLogosBoundaryCheck();
            }, 6000);
        },
        // A setTimeout matched to the track's 600ms CSS transition, not a
        // `transitionend` listener: in testing, transitionend on this
        // transform-only, will-change:transform track did not reliably
        // fire (the transform still visibly animated — just no completion
        // event), which left logosIndex counting up forever past the real
        // item range instead of wrapping. A timer keyed to the known
        // transition duration sidesteps that unreliability entirely.
        scheduleLogosBoundaryCheck() {
            clearTimeout(this.logosBoundaryTimer);
            this.logosBoundaryTimer = setTimeout(() => {
                const len = partnerLogos.length;
                if (this.logosIndex >= len || this.logosIndex < 0) {
                    const normalized = ((this.logosIndex % len) + len) % len;
                    this.logosInstant = true;
                    this.logosIndex = normalized;
                    requestAnimationFrame(() => requestAnimationFrame(() => { this.logosInstant = false; }));
                }
            }, 650);
        },
        logosDragStart(e) {
            if (!e.isPrimary || e.button !== 0) return;
            clearInterval(this.logosTimer);
            clearTimeout(this.logosBoundaryTimer);
            this.logosDragging = true;
            this.logosDidDrag = false;
            this.logosDragStartX = e.clientX;
            this.logosDragBaseIndex = this.logosIndex;
            this.measureLogosStep();
        },
        logosDragMove(e) {
            if (!this.logosDragging) return;
            const offset = e.clientX - this.logosDragStartX;
            // Preserve link clicks until an actual swipe begins.
            if (!this.logosDidDrag && Math.abs(offset) < 8) return;
            if (!this.logosDidDrag) {
                this.logosDidDrag = true;
                try { e.currentTarget.setPointerCapture(e.pointerId); } catch (err) {}
            }
            this.logosDragOffsetPx = offset;
        },
        logosDragEnd() {
            if (!this.logosDragging) return;
            this.logosDragging = false;
            const deltaSteps = Math.round(this.logosDragOffsetPx / this.logosStep);
            this.logosIndex = this.logosDragBaseIndex - deltaSteps;
            this.logosDragOffsetPx = 0;
            this.scheduleLogosBoundaryCheck();
            this.restartLogosAutoplay();
        },
    }));
}
