@props([
    'action',
    'title' => 'Delete this item?',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Delete',
])

<div
    class="inline-flex"
    x-data="{
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
            if (!this.open) return;
            const root = this.$refs.dialog;
            if (!root) return;
            const list = Array.from(root.querySelectorAll('button')).filter((el) => !el.disabled);
            if (!list.length) return;
            const first = list[0];
            const last = list[list.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    }"
    @keydown.escape.window="if (open) closeModal()"
>
    <div @click="openModal()">
        {{ $trigger ?? $slot }}
    </div>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            class="fixed inset-0 z-[80] flex items-center justify-center p-4"
            role="presentation"
        >
            <div class="absolute inset-0 bg-fs-primary/50" @click="closeModal()" aria-hidden="true"></div>

            <div
                x-ref="dialog"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="confirm-delete-title"
                aria-describedby="confirm-delete-desc"
                class="relative z-10 w-full max-w-md rounded-md bg-white p-6 shadow-lg border border-black/10"
                @keydown.tab="trap($event)"
                @click.stop
            >
                <h2 id="confirm-delete-title" class="text-lg font-semibold text-fs-primary">{{ $title }}</h2>
                <p id="confirm-delete-desc" class="mt-2 text-sm text-fs-secondary">{{ $message }}</p>

                <form method="POST" action="{{ $action }}" class="mt-6 flex flex-wrap justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <x-button type="button" variant="ghost" @click="closeModal()">Cancel</x-button>
                    <x-button type="submit" variant="danger" x-ref="confirmBtn">{{ $confirmLabel }}</x-button>
                </form>
            </div>
        </div>
    </template>
</div>
