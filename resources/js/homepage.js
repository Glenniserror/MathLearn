/* =====================================================================
   MathLearn — homepage.js
   Nav state + mobile menu · scroll reveal (once) · stat counters (once)
   · hero parallax. Animates transform/opacity only. Every effect is
   skipped or simplified under prefers-reduced-motion.
   ===================================================================== */

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ================= NAV: SOLID ON SCROLL + MOBILE MENU =================
const nav = document.querySelector('[data-nav]');

if (nav) {
    const toggle = nav.querySelector('[data-nav-toggle]');
    const panel = nav.querySelector('[data-nav-panel]');
    let ticking = false;

    const syncScrollState = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 8);
        ticking = false;
    };

    syncScrollState();
    window.addEventListener(
        'scroll',
        () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(syncScrollState);
            }
        },
        { passive: true }
    );

    if (toggle && panel) {
        const setOpen = (open) => {
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            panel.classList.toggle('is-open', open);
        };

        const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

        toggle.addEventListener('click', () => setOpen(!isOpen()));

        // Picking a link closes the menu.
        panel.addEventListener('click', (e) => {
            if (e.target.closest('a')) setOpen(false);
        });

        // Escape closes and returns focus to the button.
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isOpen()) {
                setOpen(false);
                toggle.focus();
            }
        });

        // Tapping outside closes it.
        document.addEventListener('click', (e) => {
            if (isOpen() && !nav.contains(e.target)) setOpen(false);
        });

        // Growing to the desktop layout resets the menu.
        window.matchMedia('(min-width: 900px)').addEventListener('change', (e) => {
            if (e.matches) setOpen(false);
        });
    }
}

// ================= SCROLL REVEAL (once per element) =================
// Elements start hidden only when html.js is set (inline script in <head>).
const revealTargets = Array.from(document.querySelectorAll('.reveal'));

// Drop the reveal classes once the transition ends so hover styles take over.
const settle = (el) => {
    let finished = false;

    const finish = () => {
        if (finished) return;
        finished = true;
        el.classList.remove('reveal', 'is-in');
        el.style.removeProperty('--reveal-delay');
    };

    el.addEventListener('transitionend', (e) => {
        if (e.target === el && e.propertyName === 'opacity') finish();
    });
    window.setTimeout(finish, 1600); // safety net
};

if (!('IntersectionObserver' in window)) {
    revealTargets.forEach((el) => el.classList.remove('reveal'));
} else {
    const revealObserver = new IntersectionObserver(
        (entries) => {
            let n = 0; // stagger only elements that arrive in the same batch
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                revealObserver.unobserve(el);

                if (!reduceMotion) {
                    el.style.setProperty('--reveal-delay', `${Math.min(n, 5) * 70}ms`);
                    n += 1;
                }

                el.classList.add('is-in');
                settle(el);
            });
        },
        { threshold: 0.15 }
    );

    revealTargets.forEach((el) => revealObserver.observe(el));
}

// ================= STAT COUNTERS (count up once) =================
const counters = Array.from(document.querySelectorAll('[data-count]'));

if (counters.length && 'IntersectionObserver' in window && !reduceMotion) {
    const format = (el, value) => {
        const suffix = el.dataset.suffix || '';
        return `${Math.round(value).toLocaleString('en-PH')}${suffix}`;
    };

    const run = (el) => {
        const target = Number(el.dataset.count) || 0;
        const duration = 1200;
        const start = performance.now();

        const frame = (now) => {
            const t = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(2, -10 * t); // ease-out expo
            el.textContent = format(el, target * eased);
            if (t < 1) requestAnimationFrame(frame);
        };

        requestAnimationFrame(frame);
    };

    const counterObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                counterObserver.unobserve(entry.target);
                run(entry.target);
            });
        },
        { threshold: 0.6 }
    );

    counters.forEach((el) => {
        el.textContent = format(el, 0);
        counterObserver.observe(el);
    });
}

// ================= HERO PARALLAX (desktop only, tiny) =================
const hero = document.querySelector('.hero');
const parallaxEl = document.querySelector('[data-parallax]');

if (hero && parallaxEl && !reduceMotion && 'IntersectionObserver' in window) {
    const desktop = window.matchMedia('(min-width: 1024px)');
    let heroVisible = true;
    let frame = 0;

    const update = () => {
        frame = 0;
        if (!desktop.matches) {
            parallaxEl.style.transform = '';
            return;
        }
        const y = Math.min(window.scrollY, window.innerHeight);
        parallaxEl.style.transform = `translate3d(0, ${(y * 0.08).toFixed(1)}px, 0)`;
    };

    new IntersectionObserver(([entry]) => {
        heroVisible = entry.isIntersecting;
        if (heroVisible) update();
    }).observe(hero);

    window.addEventListener(
        'scroll',
        () => {
            if (heroVisible && !frame) frame = requestAnimationFrame(update);
        },
        { passive: true }
    );

    desktop.addEventListener('change', update);
}

// ================= DATA-DRIVEN BAR WIDTHS =================
// Blade prints the real percentage in data-width; setting it here (CSSOM)
// avoids inline style attributes, which a strict CSP would block.
document.querySelectorAll('[data-width]').forEach((el) => {
    const value = Math.max(0, Math.min(100, Number(el.dataset.width) || 0));
    el.style.width = `${value}%`;
});

// ================= LEGAL PANELS =================
// #privacy and #terms (footer, CTA line, sign-up page) open the matching
// <details> on this page. One panel open at a time keeps the page short.
const legalPanels = document.querySelectorAll('details[data-legal]');

function openLegalFromHash() {
    const id = decodeURIComponent(window.location.hash.slice(1));
    if (!id) return;
    const target = [...legalPanels].find((panel) => panel.id === id);
    if (!target) return;

    legalPanels.forEach((panel) => { if (panel !== target) panel.open = false; });
    target.open = true;
    target.scrollIntoView({ block: 'start', behavior: reduceMotion ? 'auto' : 'smooth' });
}

if (legalPanels.length) {
    window.addEventListener('hashchange', openLegalFromHash);
    openLegalFromHash();
}