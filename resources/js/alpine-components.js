/** Named Alpine.data components for CSP-safe Alpine (@alpinejs/csp). */

function parseAlpineConfig(el) {
    const raw = el?.getAttribute?.('data-alpine-config');
    if (!raw) {
        return {};
    }
    try {
        return JSON.parse(raw);
    } catch {
        return {};
    }
}

export function registerAlpineComponents(Alpine) {
    Alpine.data('adminShell', () => ({
        sidebarOpen: false,
        isDesktop: window.matchMedia('(min-width: 1024px)').matches,
        init() {
            const mq = window.matchMedia('(min-width: 1024px)');
            const sync = (e) => {
                this.isDesktop = e.matches;
                if (e.matches) {
                    this.sidebarOpen = false;
                }
            };
            mq.addEventListener('change', sync);
        },
        get showOverlay() {
            return this.sidebarOpen && !this.isDesktop;
        },
        get sidebarHidden() {
            return !this.isDesktop && !this.sidebarOpen;
        },
        sidebarClass() {
            return this.sidebarOpen ? 'translate-x-0' : '-translate-x-full';
        },
        ariaHidden() {
            return this.sidebarHidden ? 'true' : 'false';
        },
        openSidebar() {
            this.sidebarOpen = true;
        },
        closeSidebar() {
            this.sidebarOpen = false;
        },
    }));

    Alpine.data('authCard', () => ({
        mode: 'login',
        urls: {},
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.mode = cfg.mode || 'login';
            this.urls = cfg.urls || {};
        },
        get isLogin() {
            return this.mode === 'login';
        },
        get isForgot() {
            return this.mode === 'forgot';
        },
        get isRegister() {
            return this.mode === 'register';
        },
        setMode(next) {
            if (this.mode === next) {
                return;
            }
            this.mode = next;
            const url = this.urls[next];
            if (url && window.location.pathname !== new URL(url, window.location.origin).pathname) {
                window.history.replaceState({}, '', url);
            }
        },
    }));

    Alpine.data('navBar', () => ({
        navOpen: false,
        profileOpen: false,
        localeOpen: false,
        openNav() {
            this.navOpen = true;
        },
        closeNav() {
            this.navOpen = false;
        },
        toggleProfile() {
            this.profileOpen = !this.profileOpen;
        },
        closeProfile() {
            this.profileOpen = false;
        },
        toggleLocale() {
            this.localeOpen = !this.localeOpen;
        },
        closeLocale() {
            this.localeOpen = false;
        },
        closeOverlays() {
            this.navOpen = false;
            this.profileOpen = false;
            this.localeOpen = false;
        },
        profileExpanded() {
            return this.profileOpen ? 'true' : 'false';
        },
        localeExpanded() {
            return this.localeOpen ? 'true' : 'false';
        },
        submitLocaleForm() {
            const form = this.$refs.localeForm;
            if (form) {
                form.submit();
            }
        },
    }));

    Alpine.data('notifCount', () => ({
        count: 0,
        url: '',
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.count = Number(cfg.count) || 0;
            this.url = cfg.url || '';
            if (!this.url) {
                return;
            }
            setInterval(async () => {
                try {
                    const r = await fetch(this.url, { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    this.count = j.count ?? 0;
                } catch {
                    /* ignore */
                }
            }, 60000);
        },
        get hasCount() {
            return this.count > 0;
        },
    }));

    Alpine.data('dismissible', () => ({
        show: true,
        dismiss() {
            this.show = false;
        },
    }));

    Alpine.data('tabs', () => ({
        tab: null,
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.tab = cfg.tab != null ? cfg.tab : null;
        },
        setTab(key) {
            this.tab = key;
        },
        isActive(key) {
            return this.tab === key;
        },
        tabClass(key) {
            return this.isActive(key)
                ? 'border-fs-brand text-fs-primary'
                : 'border-transparent text-fs-muted hover:text-fs-primary';
        },
        ariaSelected(key) {
            return this.isActive(key) ? 'true' : 'false';
        },
    }));

    Alpine.data('modal', () => ({
        open: false,
        name: 'modal',
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.name = cfg.name || 'modal';
        },
        openModal() {
            this.open = true;
        },
        closeModal() {
            this.open = false;
        },
        onOpenEvent(e) {
            if (e.detail === this.name) {
                this.open = true;
            }
        },
        onCloseEvent(e) {
            if (e.detail === this.name) {
                this.open = false;
            }
        },
    }));

    Alpine.data('faqItem', () => ({
        open: false,
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.open = !!cfg.open;
        },
        toggle() {
            this.open = !this.open;
        },
        chevronClass() {
            return this.open ? 'rotate-180' : '';
        },
        ariaExpanded() {
            return this.open ? 'true' : 'false';
        },
    }));

    Alpine.data('filtersPanel', () => ({
        filtersOpen: false,
        openFilters() {
            this.filtersOpen = true;
        },
        closeFilters() {
            this.filtersOpen = false;
        },
        submitSort(el) {
            if (el && el.form) {
                el.form.submit();
            }
        },
    }));

    Alpine.data('testimonialSlider', () => ({
        slide: 0,
        setSlide(i) {
            this.slide = i;
        },
        isSlide(i) {
            return this.slide === i;
        },
        get trackStyle() {
            return 'transform: translateX(-' + this.slide * 100 + '%)';
        },
        dotClass(i) {
            return this.isSlide(i) ? 'bg-fs-brand' : 'bg-slate-300';
        },
    }));

    Alpine.data('confirmDelete', () => ({
        open: false,
        previouslyFocused: null,
        openModal() {
            this.previouslyFocused = document.activeElement;
            this.open = true;
            this.$nextTick(() => this.$refs.confirmBtn && this.$refs.confirmBtn.focus());
        },
        closeModal() {
            this.open = false;
            this.$nextTick(() => {
                if (this.previouslyFocused && this.previouslyFocused.focus) {
                    this.previouslyFocused.focus();
                }
            });
        },
        closeOnEscape() {
            if (this.open) {
                this.closeModal();
            }
        },
        trap(e) {
            if (!this.open) {
                return;
            }
            const root = this.$refs.dialog;
            if (!root) {
                return;
            }
            const list = Array.from(root.querySelectorAll('button')).filter((el) => !el.disabled);
            if (!list.length) {
                return;
            }
            const first = list[0];
            const last = list[list.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        },
    }));

    Alpine.data('listingForm', () => ({
        listingTypeId: '',
        types: {},
        attrValues: {},
        featuredPreview: null,
        galleryPreviews: [],
        featuredObjectUrl: null,
        galleryObjectUrls: [],
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.listingTypeId = cfg.listingTypeId != null ? cfg.listingTypeId : '';
            this.types = cfg.types || {};
            this.attrValues = cfg.attrValues || {};
        },
        get dynamicAttributes() {
            const type = this.types[this.listingTypeId];
            return type ? type.attributes : [];
        },
        get hasDynamicAttributes() {
            return this.dynamicAttributes.length > 0;
        },
        get hasGalleryPreviews() {
            return this.galleryPreviews.length > 0;
        },
        attrId(key) {
            return 'attr_' + key;
        },
        attrName(key) {
            return 'attributes[' + key + ']';
        },
        attrValue(key) {
            const v = this.attrValues[key];
            return v === undefined || v === null ? '' : v;
        },
        isAttrChecked(key) {
            const v = this.attrValues[key];
            return v == 1 || v === true || v === '1';
        },
        isOptSelected(key, value) {
            return String(this.attrValue(key)) === String(value);
        },
        isInputType(attr, type) {
            return attr.input_type === type;
        },
        galleryAlt(index) {
            return 'Preview of new gallery image ' + (index + 1);
        },
        optionEntries(options) {
            if (!options) {
                return [];
            }
            if (Array.isArray(options)) {
                return options.map((v) => ({ value: String(v), label: String(v) }));
            }
            return Object.keys(options).map((k) => ({ value: String(k), label: String(options[k]) }));
        },
        onFeaturedChange(event) {
            if (this.featuredObjectUrl) {
                URL.revokeObjectURL(this.featuredObjectUrl);
                this.featuredObjectUrl = null;
            }
            const file = event.target.files && event.target.files[0];
            if (!file) {
                this.featuredPreview = null;
                return;
            }
            this.featuredObjectUrl = URL.createObjectURL(file);
            this.featuredPreview = this.featuredObjectUrl;
        },
        onGalleryChange(event) {
            this.galleryObjectUrls.forEach((url) => URL.revokeObjectURL(url));
            this.galleryObjectUrls = [];
            this.galleryPreviews = [];
            const files = event.target.files ? Array.from(event.target.files) : [];
            files.forEach((file) => {
                const url = URL.createObjectURL(file);
                this.galleryObjectUrls.push(url);
                this.galleryPreviews.push(url);
            });
        },
        destroy() {
            if (this.featuredObjectUrl) {
                URL.revokeObjectURL(this.featuredObjectUrl);
            }
            this.galleryObjectUrls.forEach((url) => URL.revokeObjectURL(url));
        },
    }));

    Alpine.data('imageOrder', () => ({
        order: [],
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.order = Array.isArray(cfg.order) ? cfg.order : [];
        },
        moveUp(id) {
            const i = this.order.indexOf(id);
            if (i > 0) {
                const t = this.order[i - 1];
                this.order[i - 1] = this.order[i];
                this.order[i] = t;
            }
        },
        moveDown(id) {
            const i = this.order.indexOf(id);
            if (i > -1 && i < this.order.length - 1) {
                const t = this.order[i + 1];
                this.order[i + 1] = this.order[i];
                this.order[i] = t;
            }
        },
    }));

    Alpine.data('permissionMatrix', () => ({
        toggleGroup(btn, checked) {
            const fieldset = btn.closest('fieldset');
            if (!fieldset) {
                return;
            }
            fieldset.querySelectorAll('input[data-perm]').forEach((box) => {
                box.checked = checked;
            });
        },
        syncSelectAll(box) {
            const fieldset = box.closest('fieldset');
            if (!fieldset) {
                return;
            }
            const boxes = [...fieldset.querySelectorAll('input[data-perm]')];
            const selectAll = fieldset.querySelector('input[data-select-all]');
            if (selectAll) {
                selectAll.checked = boxes.length > 0 && boxes.every((b) => b.checked);
                selectAll.indeterminate = !selectAll.checked && boxes.some((b) => b.checked);
            }
        },
    }));

    Alpine.data('listingTypeForm', () => ({
        rows: [],
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.rows = Array.isArray(cfg.rows) ? cfg.rows : [];
            if (this.rows.length === 0) {
                this.addRow();
            }
        },
        addRow() {
            this.rows.push({
                key: '',
                label: '',
                input_type: 'text',
                options: '',
                is_required: false,
                is_filterable: false,
                sort_order: this.rows.length,
            });
        },
        removeRow(index) {
            this.rows.splice(index, 1);
            if (this.rows.length === 0) {
                this.addRow();
            }
        },
    }));

    Alpine.data('bookingQuote', () => ({
        quoteUrl: '',
        roomTypeId: null,
        checkIn: '',
        checkOut: '',
        nights: 0,
        total: 0,
        available: false,
        availableRooms: 0,
        loaded: false,
        init() {
            const cfg = parseAlpineConfig(this.$el);
            this.quoteUrl = cfg.quoteUrl || '';
            this.roomTypeId = cfg.roomTypeId;
        },
        get totalLabel() {
            return Number(this.total || 0).toLocaleString();
        },
        get nightsLabel() {
            return this.nights + ' night(s)';
        },
        get roomsLeftLabel() {
            return this.availableRooms + ' room(s) left';
        },
        get showUnavailable() {
            return !this.available && !!this.checkIn && !!this.checkOut;
        },
        async fetchQuote() {
            if (!this.checkIn || !this.checkOut || this.checkOut <= this.checkIn) {
                this.loaded = false;
                this.available = false;
                return;
            }
            const params = new URLSearchParams({
                room_type_id: this.roomTypeId,
                check_in: this.checkIn,
                check_out: this.checkOut,
            });
            const res = await fetch(this.quoteUrl + '?' + params.toString(), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) {
                this.available = false;
                this.loaded = true;
                return;
            }
            const data = await res.json();
            this.nights = data.nights;
            this.total = data.total;
            this.available = data.available;
            this.availableRooms = data.available_rooms;
            this.loaded = true;
        },
    }));
}
