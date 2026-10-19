/**
 * Maskanoor motion — vanilla ES module (~carousel, hero, reveal, search helpers).
 * Progressive enhancement: page works without JS.
 */

const REDUCE = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/** Hero slideshow interval and progress-bar duration share this single constant (ms). */
const HERO_SLIDE_MS = 4000;

function qs(sel, root = document) {
    return root.querySelector(sel);
}

function qsa(sel, root = document) {
    return [...root.querySelectorAll(sel)];
}

function initJsFlag() {
    document.documentElement.classList.add('js');
    if (REDUCE()) {
        document.documentElement.classList.add('reduce-motion');
    }
}

function initReveal() {
    const nodes = qsa('[data-reveal]');
    if (!nodes.length) return;

    const show = (el) => {
        if (!el || el.classList.contains('is-visible')) return;
        el.classList.add('is-visible');
    };

    if (REDUCE() || !('IntersectionObserver' in window)) {
        nodes.forEach(show);
        return;
    }

    // Cap concurrent animations (~6) without dropping elements that already intersect
    // (hash jumps / scroll-mt used to leave sections blank until a manual scroll).
    let active = 0;
    const queue = [];
    const flush = () => {
        while (active < 6 && queue.length) {
            const el = queue.shift();
            if (!el || el.classList.contains('is-visible')) continue;
            active += 1;
            show(el);
            setTimeout(() => {
                active = Math.max(0, active - 1);
                flush();
            }, 120);
        }
    };
    const enqueue = (el) => {
        if (!el || el.classList.contains('is-visible')) return;
        if (!queue.includes(el)) queue.push(el);
        flush();
    };

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                io.unobserve(entry.target);
                enqueue(entry.target);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );

    const revealInViewport = () => {
        const vh = window.innerHeight || document.documentElement.clientHeight;
        nodes.forEach((el) => {
            if (el.classList.contains('is-visible')) return;
            const r = el.getBoundingClientRect();
            if (r.bottom > 0 && r.top < vh) enqueue(el);
        });
    };

    const revealHashTarget = () => {
        const id = (location.hash || '').replace(/^#/, '');
        if (!id) return;
        const target = document.getElementById(id);
        if (!target) return;
        if (target.hasAttribute('data-reveal')) enqueue(target);
        qsa('[data-reveal]', target).forEach(enqueue);
    };

    nodes.forEach((el) => io.observe(el));

    const sync = () => {
        revealHashTarget();
        revealInViewport();
    };

    // After layout / native hash scroll (incl. scroll-margin-top).
    requestAnimationFrame(() => requestAnimationFrame(sync));
    window.addEventListener('hashchange', () => requestAnimationFrame(sync));
    window.addEventListener('load', sync, { once: true });
}

function initBreak() {
    qsa('[data-break]').forEach((el) => {
        if (REDUCE() || !('IntersectionObserver' in window)) {
            el.classList.add('is-inview');
            return;
        }
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-inview');
                        io.unobserve(e.target);
                    }
                });
            },
            { threshold: 0.2 }
        );
        io.observe(el);
    });
}

function initHeroSlideshow() {
    const root = qs('[data-hero-slideshow]');
    if (!root) return;

    const slides = qsa('.fs-hero-slide', root);
    const dots = qsa('[data-hero-dot]', root);
    const progress = qs('[data-hero-progress]', root);
    const btnPrev = qs('[data-hero-prev]', root);
    const btnNext = qs('[data-hero-next]', root);
    const btnPause = qs('[data-hero-pause]', root);
    const live = qs('[data-hero-live]', root);
    let index = 0;
    let paused = REDUCE();
    let timer = null;
    let visible = true;
    let onScreen = true;

    const loadRest = () => {
        slides.forEach((slide, i) => {
            if (i === 0) return;
            qsa('img[data-src]', slide).forEach((img) => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    delete img.dataset.src;
                }
                if (img.dataset.srcset) {
                    img.srcset = img.dataset.srcset;
                    delete img.dataset.srcset;
                }
            });
            qsa('source[data-srcset]', slide).forEach((s) => {
                if (s.dataset.srcset) {
                    s.srcset = s.dataset.srcset;
                    delete s.dataset.srcset;
                }
            });
        });
    };

    const go = (next) => {
        slides[index]?.classList.remove('is-active');
        index = (next + slides.length) % slides.length;
        slides[index]?.classList.add('is-active');
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });
        if (live && paused) {
            live.textContent = `Slide ${index + 1} of ${slides.length}`;
        }
        restartTimer();
    };

    const clear = () => {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    };

    const restartTimer = () => {
        clear();
        if (paused || !visible || !onScreen || REDUCE() || slides.length < 2) return;
        timer = setInterval(() => go(index + 1), HERO_SLIDE_MS);
        if (progress) {
            progress.style.transition = 'none';
            progress.style.width = '0%';
            requestAnimationFrame(() => {
                progress.style.transition = REDUCE() ? 'none' : `width ${HERO_SLIDE_MS}ms linear`;
                progress.style.width = '100%';
            });
        }
    };

    const setPaused = (val) => {
        paused = val;
        if (btnPause) {
            btnPause.setAttribute('aria-pressed', paused ? 'true' : 'false');
            btnPause.textContent = paused ? 'Play' : 'Pause';
        }
        restartTimer();
    };

    btnPrev?.addEventListener('click', () => go(index - 1));
    btnNext?.addEventListener('click', () => go(index + 1));
    btnPause?.addEventListener('click', () => setPaused(!paused));
    dots.forEach((dot, i) => dot.addEventListener('click', () => go(i)));

    root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            go(index - 1);
        }
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            go(index + 1);
        }
    });

    let touchX = null;
    root.addEventListener(
        'touchstart',
        (e) => {
            touchX = e.changedTouches[0].clientX;
        },
        { passive: true }
    );
    root.addEventListener(
        'touchend',
        (e) => {
            if (touchX == null) return;
            const dx = e.changedTouches[0].clientX - touchX;
            if (Math.abs(dx) > 40) go(dx < 0 ? index + 1 : index - 1);
            touchX = null;
        },
        { passive: true }
    );

    root.addEventListener('mouseenter', () => {
        if (!REDUCE()) setPaused(true);
    });
    root.addEventListener('mouseleave', () => {
        if (!REDUCE()) setPaused(false);
    });
    root.addEventListener('focusin', () => setPaused(true));
    root.addEventListener('focusout', (e) => {
        if (!root.contains(e.relatedTarget)) setPaused(false);
    });

    document.addEventListener('visibilitychange', () => {
        visible = document.visibilityState === 'visible';
        restartTimer();
    });

    if ('IntersectionObserver' in window) {
        new IntersectionObserver(
            (entries) => {
                onScreen = entries[0]?.isIntersecting ?? true;
                restartTimer();
            },
            { threshold: 0.2 }
        ).observe(root);
    }

    const loadIn = qs('.fs-hero-load', root);
    requestAnimationFrame(() => loadIn?.classList.add('is-ready'));

    if (document.readyState === 'complete') {
        loadRest();
    } else {
        window.addEventListener('load', () => {
            if ('requestIdleCallback' in window) {
                requestIdleCallback(loadRest);
            } else {
                setTimeout(loadRest, 200);
            }
        });
    }

    slides[0]?.classList.add('is-active');
    dots[0]?.setAttribute('aria-current', 'true');
    restartTimer();
}

function initCarousels() {
    qsa('[data-carousel]').forEach((root) => {
        const track = qs('[data-carousel-track]', root);
        const prev = qs('[data-carousel-prev]', root);
        const next = qs('[data-carousel-next]', root);
        const controls = qs('[data-carousel-controls]', root);
        const thumb = qs('[data-carousel-thumb]', root);
        if (!track) return;

        const slides = qsa('[data-carousel-slide]', track);
        if (slides.length === 0) return;

        const update = () => {
            const max = track.scrollWidth - track.clientWidth;
            const atStart = track.scrollLeft <= 2;
            const atEnd = track.scrollLeft >= max - 2;
            if (prev) prev.disabled = atStart;
            if (next) next.disabled = atEnd;
            if (thumb && max > 0) {
                thumb.style.width = `${Math.min(100, ((track.scrollLeft + track.clientWidth) / track.scrollWidth) * 100)}%`;
            }
            const fits = track.scrollWidth <= track.clientWidth + 4;
            if (controls) controls.hidden = fits;
        };

        const scrollByPage = (dir) => {
            const amount = Math.max(track.clientWidth * 0.85, 240);
            track.scrollBy({ left: dir * amount, behavior: REDUCE() ? 'auto' : 'smooth' });
        };

        prev?.addEventListener('click', () => scrollByPage(-1));
        next?.addEventListener('click', () => scrollByPage(1));
        track.addEventListener('scroll', update, { passive: true });
        track.addEventListener('scrollend', update);
        window.addEventListener('resize', update);

        let dragging = false;
        let startX = 0;
        let startScroll = 0;
        track.addEventListener('pointerdown', (e) => {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            dragging = true;
            startX = e.clientX;
            startScroll = track.scrollLeft;
            track.classList.add('is-dragging');
            track.setPointerCapture?.(e.pointerId);
        });
        track.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            track.scrollLeft = startScroll - (e.clientX - startX);
        });
        const endDrag = () => {
            dragging = false;
            track.classList.remove('is-dragging');
            update();
        };
        track.addEventListener('pointerup', endDrag);
        track.addEventListener('pointercancel', endDrag);

        track.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                scrollByPage(-1);
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                scrollByPage(1);
            }
        });

        update();
    });
}

function initSearch() {
    const roots = qsa('[data-property-search]');
    roots.forEach((root) => setupSearchRoot(root));
}

function setupSearchRoot(root) {
    const form = qs('form', root);
    if (!form) return;

    const purposeInput = qs('[name="purpose"]', form);
    const tabs = qsa('[data-purpose-tab]', root);
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((t) => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');
            if (purposeInput) purposeInput.value = tab.dataset.purposeTab || '';
        });
    });

    setupCombobox(root);
    setupTypePopover(root);
    setupBudgetPopover(root);
    setupMobileSheet(root);

    // Outside click / Esc — closes type/budget popovers and location list
    document.addEventListener('pointerdown', (e) => {
        if (!root.contains(e.target)) {
            closeAllPopovers(root);
            return;
        }
        // Click inside search but outside the open location combobox → close list
        const locInput = qs('[data-location-input]', root);
        const locList = qs('[data-location-list]', root);
        if (locList && !locList.hidden) {
            const inLoc = locInput?.contains(e.target) || locList.contains(e.target);
            if (!inLoc) closeLocationList(root);
        }
        // Click outside an open type/budget popover (but still in search) → close it
        qsa('[data-popover].is-open, [data-popover]:not([hidden])', root).forEach((p) => {
            if (p.hidden) return;
            const trigger = p.id === 'type-popover'
                ? qs('[data-type-trigger]', root)
                : qs('[data-budget-trigger]', root);
            if (!p.contains(e.target) && !trigger?.contains(e.target)) {
                p.classList.remove('is-open');
                p.hidden = true;
                trigger?.setAttribute('aria-expanded', 'false');
            }
        });
    });
    root.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllPopovers(root);
    });
}

function closeLocationList(root) {
    const list = qs('[data-location-list]', root);
    const input = qs('[data-location-input]', root);
    if (list) {
        list.hidden = true;
        list.classList.remove('is-open');
    }
    if (input) input.setAttribute('aria-expanded', 'false');
}

function closeAllPopovers(root) {
    qsa('[data-popover]', root).forEach((p) => {
        p.classList.remove('is-open');
        p.hidden = true;
    });
    closeLocationList(root);
    qsa('[aria-expanded="true"]', root).forEach((b) => b.setAttribute('aria-expanded', 'false'));
}

function setupCombobox(root) {
    const input = qs('[data-location-input]', root);
    const list = qs('[data-location-list]', root);
    if (!input || !list) return;

    const suggestions = JSON.parse(root.dataset.suggestions || '[]');
    let active = -1;

    const render = (q) => {
        const query = (q || '').toLowerCase().trim();
        const items = suggestions
            .filter((s) => !query || String(s).toLowerCase().includes(query))
            .slice(0, 8);
        list.innerHTML = items
            .map(
                (s, i) =>
                    `<li role="option" id="loc-opt-${i}" data-value="${escapeAttr(s)}" class="px-3 py-2.5 text-sm cursor-pointer hover:bg-[var(--color-ivory-50)] ${i === active ? 'bg-[var(--color-ivory-50)]' : ''}" aria-selected="${i === active}">${escapeHtml(s)}</li>`
            )
            .join('');
        list.hidden = items.length === 0;
        list.classList.toggle('is-open', items.length > 0);
        input.setAttribute('aria-expanded', items.length > 0 ? 'true' : 'false');
    };

    input.addEventListener('input', () => {
        active = -1;
        render(input.value);
    });
    input.addEventListener('focus', () => render(input.value));
    input.addEventListener('keydown', (e) => {
        const opts = qsa('[role="option"]', list);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            active = Math.min(active + 1, opts.length - 1);
            render(input.value);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            active = Math.max(active - 1, 0);
            render(input.value);
        } else if (e.key === 'Enter' && active >= 0 && opts[active]) {
            e.preventDefault();
            input.value = opts[active].dataset.value || '';
            closeList();
        } else if (e.key === 'Escape') {
            closeList();
        }
    });

    list.addEventListener('click', (e) => {
        const li = e.target.closest('[role="option"]');
        if (!li) return;
        input.value = li.dataset.value || '';
        closeList();
    });

    function closeList() {
        active = -1;
        closeLocationList(root);
    }
}

function setupTypePopover(root) {
    const trigger = qs('[data-type-trigger]', root);
    const pop = qs('[data-type-popover]', root);
    const hidden = qs('[name="type"]', root);
    const label = qs('[data-type-label]', root);
    if (!trigger || !pop) return;

    trigger.addEventListener('click', () => togglePopover(trigger, pop));

    pop.addEventListener('change', (e) => {
        const radio = e.target.closest('input[type="radio"]');
        if (!radio) return;
        if (hidden) hidden.value = radio.value;
        if (label) label.textContent = radio.dataset.label || radio.value || 'Any type';
        closeAllPopovers(root);
    });
}

function setupBudgetPopover(root) {
    const trigger = qs('[data-budget-trigger]', root);
    const pop = qs('[data-budget-popover]', root);
    const priceInput = qs('[name="price"]', root);
    const minInput = qs('[data-budget-min]', root);
    const maxInput = qs('[data-budget-max]', root);
    const label = qs('[data-budget-label]', root);
    if (!trigger || !pop) return;

    trigger.addEventListener('click', () => togglePopover(trigger, pop));

    qsa('[data-budget-preset]', pop).forEach((btn) => {
        btn.addEventListener('click', () => {
            const price = btn.dataset.price || '';
            if (priceInput) priceInput.value = price;
            if (minInput) minInput.value = btn.dataset.min || '';
            if (maxInput) maxInput.value = btn.dataset.max || '';
            if (label) label.textContent = btn.dataset.label || 'Any budget';
            closeAllPopovers(root);
        });
    });

    qs('[data-budget-apply]', pop)?.addEventListener('click', () => {
        const min = (minInput?.value || '').replace(/,/g, '');
        const max = (maxInput?.value || '').replace(/,/g, '');
        if (priceInput) {
            if (min && max) priceInput.value = `${min}-${max}`;
            else if (min) priceInput.value = `${min}-1000000000`;
            else if (max) priceInput.value = `0-${max}`;
            else priceInput.value = '';
        }
        const form = qs('form', root);
        // Also set price_min / price_max for backend
        setOrCreateHidden(form, 'price_min', min);
        setOrCreateHidden(form, 'price_max', max);
        if (label) {
            label.textContent =
                min || max
                    ? `${min ? formatNum(min) : '…'} – ${max ? formatNum(max) : '…'}`
                    : 'Any budget';
        }
        closeAllPopovers(root);
    });

    qs('[data-budget-clear]', pop)?.addEventListener('click', () => {
        if (minInput) minInput.value = '';
        if (maxInput) maxInput.value = '';
        if (priceInput) priceInput.value = '';
        if (label) label.textContent = 'Any budget';
        const form = qs('form', root);
        setOrCreateHidden(form, 'price_min', '');
        setOrCreateHidden(form, 'price_max', '');
    });
}

function setupMobileSheet(root) {
    const openBtn = qs('[data-sheet-open]', root);
    const sheet = qs('[data-sheet]', root);
    const backdrop = qs('[data-sheet-backdrop]', root);
    const closeBtn = qs('[data-sheet-close]', root);
    if (!openBtn || !sheet) return;

    const open = () => {
        sheet.hidden = false;
        backdrop && (backdrop.hidden = false);
        requestAnimationFrame(() => {
            sheet.classList.add('is-open');
            backdrop?.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';
        openBtn.setAttribute('aria-expanded', 'true');
        qs('button, input, select, a', sheet)?.focus();
    };

    const close = () => {
        sheet.classList.remove('is-open');
        backdrop?.classList.remove('is-open');
        document.body.style.overflow = '';
        openBtn.setAttribute('aria-expanded', 'false');
        setTimeout(() => {
            sheet.hidden = true;
            if (backdrop) backdrop.hidden = true;
        }, 300);
        openBtn.focus();
    };

    openBtn.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    backdrop?.addEventListener('click', close);
    sheet.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}

function togglePopover(trigger, pop) {
    const open = !pop.classList.contains('is-open');
    const root = trigger.closest('[data-property-search]');
    if (root) closeAllPopovers(root);
    if (open) {
        pop.hidden = false;
        pop.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    }
}

function setOrCreateHidden(form, name, value) {
    if (!form) return;
    let el = form.querySelector(`[name="${name}"]`);
    if (!el) {
        el = document.createElement('input');
        el.type = 'hidden';
        el.name = name;
        form.appendChild(el);
    }
    el.value = value || '';
}

function formatNum(n) {
    const num = Number(String(n).replace(/,/g, ''));
    if (Number.isNaN(num)) return n;
    return num.toLocaleString('en-US');
}

function escapeHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function escapeAttr(s) {
    return escapeHtml(s).replace(/'/g, '&#39;');
}

function initFaq() {
    qsa('[data-faq]').forEach((item) => {
        const btn = qs('[data-faq-toggle]', item);
        const panel = qs('[data-faq-panel]', item);
        const chevron = qs('[data-faq-chevron]', item);
        if (!btn || !panel) return;
        btn.addEventListener('click', () => {
            const open = panel.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            chevron?.classList.toggle('is-open', open);
        });
    });
}

export function initMotion() {
    initJsFlag();
    initReveal();
    initBreak();
    initHeroSlideshow();
    initCarousels();
    initSearch();
    initFaq();
}
