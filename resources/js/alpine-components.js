/** Named Alpine.data components for CSP-safe Alpine (@alpinejs/csp). */

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
    }));

    Alpine.data('authCard', (cfg) => ({
        mode: cfg.mode,
        urls: cfg.urls,
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
    }));

    Alpine.data('notifCount', (cfg) => ({
        count: Number(cfg.count) || 0,
        url: cfg.url,
        init() {
            if (!this.url) {
                return;
            }
            setInterval(async () => {
                try {
                    const r = await fetch(this.url, { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    this.count = j.count ?? 0;
                } catch (e) {
                    /* ignore */
                }
            }, 60000);
        },
    }));

    Alpine.data('dismissible', () => ({
        show: true,
    }));

    Alpine.data('tabs', (tab) => ({
        tab,
    }));

    Alpine.data('modal', () => ({
        open: false,
    }));

    Alpine.data('faqItem', (open) => ({
        open: !!open,
    }));

    Alpine.data('filtersPanel', () => ({
        filtersOpen: false,
    }));

    Alpine.data('testimonialSlider', () => ({
        slide: 0,
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

    Alpine.data('listingForm', (cfg) => ({
        listingTypeId: cfg.listingTypeId,
        types: cfg.types,
        attrValues: cfg.attrValues || {},
        featuredPreview: null,
        galleryPreviews: [],
        featuredObjectUrl: null,
        galleryObjectUrls: [],
        get dynamicAttributes() {
            const type = this.types[this.listingTypeId];
            return type ? type.attributes : [];
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

    Alpine.data('imageOrder', (order) => ({
        order: Array.isArray(order) ? order : [],
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

    Alpine.data('listingTypeForm', (cfg) => ({
        rows: cfg.rows || [],
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

    Alpine.data('bookingQuote', (cfg) => ({
        quoteUrl: cfg.quoteUrl,
        roomTypeId: cfg.roomTypeId,
        checkIn: '',
        checkOut: '',
        nights: 0,
        total: 0,
        available: false,
        availableRooms: 0,
        loaded: false,
        get totalLabel() {
            return Number(this.total || 0).toLocaleString();
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
            const res = await fetch(`${this.quoteUrl}?${params.toString()}`, {
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
