@props([
    'name' => 'modal',
    'title' => null,
    'maxWidth' => 'max-w-lg',
])

@php
    $modalConfig = ['name' => $name];
@endphp

<div
    x-data="modal"
    data-alpine-config="{{ json_encode($modalConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    x-on:open-modal.window="onOpenEvent($event)"
    x-on:close-modal.window="onCloseEvent($event)"
    x-on:keydown.escape.window="closeModal()"
    {{ $attributes }}
>
    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            @if($title) aria-labelledby="modal-title-{{ $name }}" @endif
        >
            <div class="absolute inset-0 bg-fs-primary/50" @click="closeModal()" aria-hidden="true"></div>
            <div
                x-show="open"
                x-transition.opacity.duration.150ms
                class="relative w-full {{ $maxWidth }} rounded-md bg-white border border-black/5 shadow-lg p-6"
                @click.stop
            >
                <div class="flex items-start justify-between gap-3 mb-4">
                    @if($title)
                        <h2 id="modal-title-{{ $name }}" class="fs-display text-xl text-fs-primary">{{ $title }}</h2>
                    @else
                        <span></span>
                    @endif
                    <button type="button" class="inline-flex items-center justify-center w-10 h-10 rounded-sm border border-black/10 text-fs-secondary hover:bg-black/5" @click="closeModal()" aria-label="Close dialog">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
