@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'tag' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 h-11 px-5 rounded-sm text-sm font-semibold tracking-wide transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand disabled:opacity-50 disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-fs-brand text-white hover:bg-fs-brand-dark',
        'secondary' => 'bg-fs-brand-light text-fs-primary hover:bg-[#e5d8c3]',
        'ghost' => 'bg-transparent text-fs-primary hover:bg-black/5 border border-black/10',
        'ink' => 'bg-fs-primary text-white hover:bg-[#152238]',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
    $element = $tag ?? ($href ? 'a' : 'button');
@endphp

@if($element === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
