@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'tag' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 h-11 px-5 rounded-sm text-sm font-semibold transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand disabled:opacity-50 disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-fs-brand text-white hover:bg-[#0f3d22]',
        'secondary' => 'bg-fs-brand-light text-fs-brand hover:bg-[#bbf7d0]',
        'ghost' => 'bg-transparent text-fs-primary hover:bg-slate-100 border border-slate-200',
    ];

    // List Property is the only accent-blue CTA defined in design tokens.
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
