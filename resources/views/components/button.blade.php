@props([
    'variant' => 'primary',
    'surface' => 'light', // light | dark
    'size' => 'default', // default | compact
    'type' => 'button',
    'href' => null,
    'tag' => null,
    'loading' => false,
])

@php
    $height = $size === 'compact' ? 'h-11' : 'h-12';
    $base = "inline-flex items-center justify-center gap-2 {$height} px-6 text-[14px] sm:text-[15px] font-semibold tracking-[0.01em] transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none";
    $base .= ' rounded-[var(--radius-button)] focus-visible:outline-2 focus-visible:outline-offset-2';

    if ($surface === 'dark') {
        $variants = [
            'primary' => 'bg-[var(--color-gold)] text-[var(--color-forest)] hover:brightness-95 focus-visible:outline-[var(--color-gold)]',
            'secondary' => 'bg-transparent text-[var(--color-ivory)] border border-[color-mix(in_srgb,var(--color-ivory)_60%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-ivory)_10%,transparent)] focus-visible:outline-[var(--color-gold)]',
            'ghost' => 'bg-transparent text-[var(--color-ivory)] border border-[color-mix(in_srgb,var(--color-ivory)_40%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-ivory)_10%,transparent)] focus-visible:outline-[var(--color-gold)]',
            'ink' => 'bg-[var(--color-forest-shadow)] text-[var(--color-ivory)] hover:bg-[var(--color-forest)] focus-visible:outline-[var(--color-gold)]',
            'danger' => 'bg-fs-danger text-white hover:bg-[#991b1b] focus-visible:outline-white',
        ];
    } else {
        $variants = [
            'primary' => 'bg-[var(--color-forest)] text-[var(--color-ivory)] hover:bg-[var(--color-forest-shadow)] focus-visible:outline-[var(--color-forest)]',
            'secondary' => 'bg-transparent text-[var(--color-forest)] border border-[color-mix(in_srgb,var(--color-forest)_40%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-forest)_6%,transparent)] focus-visible:outline-[var(--color-forest)]',
            'ghost' => 'bg-transparent text-[var(--color-forest)] border border-[color-mix(in_srgb,var(--color-forest)_25%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-forest)_6%,transparent)] focus-visible:outline-[var(--color-forest)]',
            'ink' => 'bg-[var(--color-forest)] text-[var(--color-ivory)] hover:bg-[var(--color-forest-shadow)] focus-visible:outline-[var(--color-forest)]',
            'danger' => 'bg-fs-danger text-white hover:bg-[#991b1b] focus-visible:outline-[var(--color-forest)]',
            // Gold-dark fill + white only when contrast is safe (~5:1)
            'gold' => 'bg-[var(--color-gold-dark)] text-white hover:brightness-95 focus-visible:outline-[var(--color-gold-dark)]',
        ];
    }

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
    $element = $tag ?? ($href ? 'a' : 'button');
@endphp

@if($element === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'aria-busy' => $loading ? 'true' : 'false']) }}>
        @if($loading)
            <span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($loading) {{ $attributes->merge(['class' => $classes, 'aria-busy' => $loading ? 'true' : 'false']) }}>
        @if($loading)
            <span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </button>
@endif
