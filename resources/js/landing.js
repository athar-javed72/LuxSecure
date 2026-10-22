/**
 * Landing page interactions — tabs, mobile nav, save hearts, back-to-top,
 * hotels booking dates, FAQ accordion, gallery lightbox.
 * No scroll-triggered reveals (design reference).
 */

function qs(sel, root = document) {
    return root.querySelector(sel);
}

function qsa(sel, root = document) {
    return [...root.querySelectorAll(sel)];
}

function initNav() {
    const toggle = qs('[data-mg-nav-toggle]');
    const drawer = qs('[data-mg-nav-drawer]');
    if (!toggle || !drawer) return;

    toggle.addEventListener('click', () => {
        const open = drawer.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    qsa('a', drawer).forEach((a) => {
        a.addEventListener('click', () => {
            drawer.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
}

function initSearchTabs() {
    qsa('[data-mg-search]').forEach((root) => {
        const tabs = qsa('[data-mg-tab]', root);
        const purpose = qs('[name="purpose"]', root);
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((t) => {
                    t.classList.remove('is-active');
                    t.setAttribute('aria-selected', 'false');
                });
                tab.classList.add('is-active');
                tab.setAttribute('aria-selected', 'true');
                if (purpose) purpose.value = tab.dataset.mgTab || '';
            });
        });
    });
}

function initHearts() {
    qsa('[data-mg-save]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const on = btn.classList.toggle('is-saved');
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
            btn.setAttribute('aria-label', on ? 'Remove from saved' : 'Save listing');
        });
    });
}

function initBackToTop() {
    const btn = qs('[data-mg-top]');
    if (!btn) return;

    const update = () => {
        btn.classList.toggle('is-visible', window.scrollY > 420);
    };

    window.addEventListener('scroll', update, { passive: true });
    update();

    btn.addEventListener('click', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
    });
}

function initBookingDates() {
    const form = qs('[data-mh-booking]');
    if (!form) return;

    const checkIn = qs('[data-mh-checkin]', form);
    const checkOut = qs('[data-mh-checkout]', form);
    const submit = qs('[data-mh-submit]', form);
    const errors = qs('[data-mh-booking-errors]');
    if (!checkIn || !checkOut) return;

    const pad = (n) => String(n).padStart(2, '0');
    const fmt = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    /** Parse YYYY-MM-DD at noon local to avoid DST/off-by-one. */
    const parseYmd = (value) => {
        const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value || '');
        if (!m) return null;
        return new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]), 12, 0, 0, 0);
    };

    const addDays = (value, days) => {
        const d = parseYmd(value);
        if (!d) return '';
        d.setDate(d.getDate() + days);
        return fmt(d);
    };

    const today = (() => {
        const n = new Date();
        return fmt(new Date(n.getFullYear(), n.getMonth(), n.getDate(), 12, 0, 0, 0));
    })();

    const showError = (message) => {
        if (!errors) return;
        errors.hidden = !message;
        errors.innerHTML = message ? `<p>${message}</p>` : '';
    };

    const syncMin = () => {
        checkIn.min = today;
        if (checkIn.value && checkIn.value < today) {
            checkIn.value = today;
        }

        if (checkIn.value) {
            const minOut = addDays(checkIn.value, 1);
            checkOut.min = minOut;
            if (checkOut.value && checkOut.value <= checkIn.value) {
                checkOut.value = '';
                showError('Check-out must be at least one night after check-in.');
            } else if (errors && !errors.dataset.server) {
                showError('');
            }
        } else {
            checkOut.min = today;
        }
    };

    checkIn.addEventListener('change', syncMin);
    checkOut.addEventListener('change', () => {
        if (checkIn.value && checkOut.value && checkOut.value <= checkIn.value) {
            checkOut.value = '';
            showError('Check-out must be at least one night after check-in.');
        } else if (errors && !errors.dataset.server) {
            showError('');
        }
    });
    syncMin();

    if (errors && errors.querySelector('p')) {
        errors.dataset.server = '1';
        errors.hidden = false;
    }

    const defaultSubmitLabel = submit?.textContent?.trim() || 'Check availability';

    const resetSubmit = () => {
        if (!submit) return;
        submit.disabled = false;
        submit.removeAttribute('aria-busy');
        submit.textContent = defaultSubmitLabel;
        form.classList.remove('is-loading');
    };

    form.addEventListener('submit', (event) => {
        if (checkIn.value && checkOut.value && checkOut.value <= checkIn.value) {
            event.preventDefault();
            showError('Check-out must be at least one night after check-in.');
            checkOut.focus();
            return;
        }

        if (submit) {
            if (submit.disabled) {
                event.preventDefault();
                return;
            }
            submit.disabled = true;
            submit.setAttribute('aria-busy', 'true');
            submit.textContent = 'Searching…';
            form.classList.add('is-loading');
        }
    });

    // bfcache / Back-Forward: restore button if the page was restored from cache
    // or the submit control was left disabled mid-navigation.
    window.addEventListener('pageshow', (event) => {
        if (event.persisted || (submit && submit.disabled)) {
            resetSubmit();
        }
    });
}

function initHotelsResultsScroll() {
    const results = qs('#results');
    if (!results) return;
    // Only jump when the URL is a search (query present) so landing stays at top.
    if (!window.location.search) return;
    requestAnimationFrame(() => {
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}

function initFaq() {
    qsa('[data-mh-faq]').forEach((root) => {
        qsa('[data-mh-faq-item]', root).forEach((item) => {
            const trigger = qs('[data-mh-faq-trigger]', item);
            if (!trigger) return;

            trigger.addEventListener('click', () => {
                const open = item.classList.toggle('is-open');
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        });
    });
}

function initGalleryLightbox() {
    const root = qs('[data-mh-gallery]');
    const lightbox = qs('[data-mh-lightbox]');
    if (!root || !lightbox) return;

    const items = qsa('[data-mh-gallery-open]', root).map((btn) => {
        const img = qs('img', btn);
        return {
            src: img?.currentSrc || img?.src || '',
            alt: img?.alt || '',
        };
    });
    if (!items.length) return;

    const imgEl = qs('[data-mh-lightbox-img]', lightbox);
    const capEl = qs('[data-mh-lightbox-cap]', lightbox);
    const closes = qsa('[data-mh-lightbox-close]', lightbox);
    const prevBtn = qs('[data-mh-lightbox-prev]', lightbox);
    const nextBtn = qs('[data-mh-lightbox-next]', lightbox);

    let index = 0;
    let lastFocus = null;

    const focusables = () =>
        qsa('button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', lightbox).filter(
            (el) => !el.hasAttribute('disabled') && el.offsetParent !== null
        );

    const show = (i) => {
        index = (i + items.length) % items.length;
        const item = items[index];
        if (imgEl) {
            imgEl.src = item.src;
            imgEl.alt = item.alt;
        }
        if (capEl) capEl.textContent = item.alt;
    };

    const open = (i) => {
        lastFocus = document.activeElement;
        show(i);
        lightbox.hidden = false;
        document.body.style.overflow = 'hidden';
        (prevBtn || qs('[data-mh-lightbox-close]', lightbox))?.focus();
    };

    const close = () => {
        lightbox.hidden = true;
        document.body.style.overflow = '';
        if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
    };

    qsa('[data-mh-gallery-open]', root).forEach((btn) => {
        btn.addEventListener('click', () => open(Number(btn.dataset.index || 0)));
    });

    closes.forEach((btn) => btn.addEventListener('click', close));
    prevBtn?.addEventListener('click', () => show(index - 1));
    nextBtn?.addEventListener('click', () => show(index + 1));

    document.addEventListener('keydown', (event) => {
        if (lightbox.hidden) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            close();
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            show(index - 1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            show(index + 1);
        } else if (event.key === 'Tab') {
            const nodes = focusables();
            if (!nodes.length) return;
            const first = nodes[0];
            const last = nodes[nodes.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initSearchTabs();
    initHearts();
    initBackToTop();
    initBookingDates();
    initHotelsResultsScroll();
    initFaq();
    initGalleryLightbox();
});
