@props([
    'tone' => 'brand',
])

@php
    $tones = [
        'brand' => 'bg-fs-brand-light text-fs-primary',
        'sale' => 'bg-fs-brand text-white',
        'rent' => 'bg-fs-accent text-white',
        'muted' => 'bg-black/5 text-fs-secondary',
        'danger' => 'bg-fs-danger/10 text-fs-danger',
        'success' => 'bg-fs-brand-light text-fs-primary',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-3 h-7 rounded-sm text-xs font-semibold tracking-wide ' . ($tones[$tone] ?? $tones['brand'])]) }}>
    {{ $slot }}
</span>
