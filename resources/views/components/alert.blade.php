@props([
    'tone' => 'info',
    'dismissible' => false,
])

@php
    $tones = [
        'success' => 'bg-fs-brand-light text-fs-primary border-fs-brand/30',
        'error' => 'bg-fs-danger/10 text-fs-danger border-fs-danger/20',
        'info' => 'bg-white text-fs-primary border-black/10',
        'warning' => 'bg-fs-brand-light/80 text-fs-primary border-fs-brand/40',
    ];
    $classes = 'relative flex gap-3 items-start p-4 rounded-sm border text-sm '.($tones[$tone] ?? $tones['info']);
@endphp

<div
    {{ $attributes->merge(['class' => $classes, 'role' => $tone === 'error' ? 'alert' : 'status']) }}
    @if($dismissible) x-data="dismissible" x-show="show" x-cloak @endif
>
    <div class="flex-1 min-w-0">{{ $slot }}</div>
    @if($dismissible)
        <button type="button" class="shrink-0 text-current/70 hover:text-current" @click="dismiss()" aria-label="Dismiss">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
