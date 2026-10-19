@props([
    'layout' => 'horizontal', // horizontal | stacked | monogram | monogram-nostar | auto
    'surface' => 'dark', // dark | light | dual (header scroll swap)
    'division' => null,
    'showTagline' => false,
    'alt' => 'Maskanoor Group',
    'width' => null,
])

@php
    use App\Support\BrandAssets;

    $resolvedLayout = $layout;
    if ($layout === 'auto') {
        $resolvedLayout = 'horizontal';
    }

    $minWidth = match ($resolvedLayout) {
        'stacked' => 140,
        'monogram', 'monogram-nostar' => 24,
        default => 120,
    };

    $styleWidth = $width ? (int) $width : $minWidth;
    $heightClass = match ($resolvedLayout) {
        'stacked' => 'h-14 sm:h-16',
        'monogram', 'monogram-nostar' => 'h-8 w-8 sm:h-9 sm:w-9',
        default => 'h-9 sm:h-10',
    };

    $darkSrc = BrandAssets::logo(
        $resolvedLayout === 'horizontal' ? 'horizontal' : $resolvedLayout,
        'dark',
        $division
    );
    $lightSrc = BrandAssets::logo(
        $resolvedLayout === 'horizontal' ? 'horizontal' : $resolvedLayout,
        'light',
        $division
    );
    $mobileDark = BrandAssets::logo('monogram-nostar', 'dark');
    $mobileLight = BrandAssets::logo('monogram-nostar', 'light');
@endphp

<span {{ $attributes->class(['inline-flex flex-col items-start gap-1']) }}>
    @if($surface === 'dual')
        {{-- Desktop / tablet horizontal lockup: swap dark↔light without layout shift --}}
        <span class="relative hidden min-[480px]:block" style="width: {{ max($styleWidth, $minWidth) }}px; height: 40px;">
            <img
                src="{{ $darkSrc }}"
                alt="{{ $alt }}"
                width="{{ max($styleWidth, $minWidth) }}"
                height="40"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-0' : 'opacity-100'"
                decoding="async"
            >
            <img
                src="{{ $lightSrc }}"
                alt=""
                aria-hidden="true"
                width="{{ max($styleWidth, $minWidth) }}"
                height="40"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-100' : 'opacity-0'"
                decoding="async"
            >
        </span>
        {{-- Under 120px / mobile: nostar monogram --}}
        <span class="relative block min-[480px]:hidden" style="width: 36px; height: 36px;">
            <img
                src="{{ $mobileDark }}"
                alt="{{ $alt }}"
                width="36"
                height="36"
                class="absolute inset-0 h-9 w-9 object-contain transition-opacity duration-200"
                :class="scrolled ? 'opacity-0' : 'opacity-100'"
                decoding="async"
            >
            <img
                src="{{ $mobileLight }}"
                alt=""
                aria-hidden="true"
                width="36"
                height="36"
                class="absolute inset-0 h-9 w-9 object-contain transition-opacity duration-200"
                :class="scrolled ? 'opacity-100' : 'opacity-0'"
                decoding="async"
            >
        </span>
    @else
        @php
            $src = BrandAssets::logo($resolvedLayout, $surface, $division);
            $mobileSrc = BrandAssets::logo('monogram-nostar', $surface);
        @endphp
        @if(in_array($resolvedLayout, ['horizontal', 'auto'], true))
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                width="{{ max($styleWidth, $minWidth) }}"
                height="40"
                class="hidden min-[480px]:block {{ $heightClass }} w-auto max-w-[min(100%,220px)] object-contain object-left"
                style="min-width: {{ $minWidth }}px;"
                decoding="async"
            >
            <img
                src="{{ $mobileSrc }}"
                alt="{{ $alt }}"
                width="36"
                height="36"
                class="block min-[480px]:hidden h-9 w-9 object-contain"
                decoding="async"
            >
        @else
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                class="{{ $heightClass }} w-auto object-contain object-left"
                style="min-width: {{ $minWidth }}px;"
                decoding="async"
            >
        @endif
    @endif

    @if($showTagline)
        <span class="fs-display text-sm italic text-current/80">{{ BrandAssets::TAGLINE }}</span>
    @endif
</span>
