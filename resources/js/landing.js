/**
 * Landing page interactions — tabs, mobile nav, save hearts, back-to-top.
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

document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initSearchTabs();
    initHearts();
    initBackToTop();
});
