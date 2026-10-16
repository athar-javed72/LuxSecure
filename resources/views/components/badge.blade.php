@props([
    'tone' => 'brand',
])

@php
    $tones = [
        'brand' => 'bg-fs-brand-light text-fs-brand',
        'sale' => 'bg-fs-brand text-white',
        'rent' => 'bg-fs-accent text-white',
        'muted' => 'bg-slate-100 text-fs-secondary',
        'danger' => 'bg-red-100 text-fs-danger',
        'success' => 'bg-fs-brand-light text-fs-brand',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-3 h-7 rounded-full text-xs font-semibold ' . ($tones[$tone] ?? $tones['brand'])]) }}>
    {{ $slot }}
</span>
