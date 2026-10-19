@props([
    'tone' => 'brand',
])

@php
    $tones = [
        'brand' => 'bg-[var(--color-ivory-50)] text-[var(--color-forest)] border border-[var(--color-border)]',
        'sale' => 'bg-[var(--color-forest)] text-[var(--color-ivory)] border border-transparent',
        'rent' => 'bg-[var(--color-gold)] text-[var(--color-forest)] border border-transparent',
        'stay' => 'bg-[var(--color-ivory-50)] text-[var(--color-forest)] border border-[var(--color-border)]',
        'muted' => 'bg-[var(--color-stone)] text-[var(--color-ink)] border border-transparent',
        'danger' => 'bg-fs-danger/10 text-fs-danger border border-transparent',
        'success' => 'bg-[var(--color-ivory-50)] text-[var(--color-forest)] border border-[var(--color-border)]',
        'warning' => 'bg-amber-100 text-amber-900 border border-transparent',
        'sample' => 'bg-[var(--color-ivory)] text-[var(--color-muted)] border border-[var(--color-border)]',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 h-6 rounded text-[11px] sm:text-xs font-semibold uppercase tracking-wide ' . ($tones[$tone] ?? $tones['brand'])]) }}>
    {{ $slot }}
</span>
