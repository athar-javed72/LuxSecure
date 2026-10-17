@props([
    'label',
    'value',
    'href' => null,
    'hint' => null,
])

@php
    $classes = 'block rounded-md bg-white shadow-sm border border-black/5 p-6 transition-shadow duration-150';
    if ($href) {
        $classes .= ' hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand';
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted">{{ $label }}</p>
        <p class="fs-display mt-2 text-2xl font-semibold text-fs-primary">{{ $value }}</p>
        @if($hint)<p class="mt-1 text-xs text-fs-secondary">{{ $hint }}</p>@endif
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted">{{ $label }}</p>
        <p class="fs-display mt-2 text-2xl font-semibold text-fs-primary">{{ $value }}</p>
        @if($hint)<p class="mt-1 text-xs text-fs-secondary">{{ $hint }}</p>@endif
        {{ $slot }}
    </div>
@endif
