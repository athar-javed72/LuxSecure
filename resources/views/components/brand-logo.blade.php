@props([
    'layout' => 'horizontal', // horizontal | compact | stacked | monogram | monogram-nostar | auto
    'surface' => 'dark', // dark | light | dual (header scroll swap)
    'division' => null,
    'showTagline' => false,
    'alt' => null,
    'width' => null,
])

@php
    use App\Support\BrandAssets;

    $resolvedLayout = $layout === 'auto' ? 'compact' : $layout;

    $minWidth = match ($resolvedLayout) {
        'stacked' => 180,
        'compact' => 180,
        'monogram', 'monogram-nostar' => 36,
        default => 160,
    };

    $styleWidth = $width ? (int) $width : $minWidth;
    $heightPx = match ($resolvedLayout) {
        'stacked' => 72,
        'compact' => 40,
        'monogram', 'monogram-nostar' => 36,
        default => 40,
    };

    $darkSrc = BrandAssets::logo($resolvedLayout, 'dark', $division);
    $lightSrc = BrandAssets::logo($resolvedLayout, 'light', $division);
    $mobileDark = BrandAssets::logo('monogram-nostar', 'dark');
    $mobileLight = BrandAssets::logo('monogram-nostar', 'light');
    $alt = $alt ?? ($siteName ?? config('app.name'));
@endphp

<span {{ $attributes->class(['inline-flex flex-col items-start gap-1']) }}>
    @if($surface === 'dual')
        <span class="relative hidden lg:block" style="width: {{ max($styleWidth, $minWidth) }}px; height: {{ $heightPx }}px;">
            <img
                src="{{ $darkSrc }}"
                alt="{{ $alt }}"
                width="{{ max($styleWidth, $minWidth) }}"
                height="{{ $heightPx }}"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-0' : 'opacity-100'"
                decoding="async"
            >
            <img
                src="{{ $lightSrc }}"
                alt=""
                aria-hidden="true"
                width="{{ max($styleWidth, $minWidth) }}"
                height="{{ $heightPx }}"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-100' : 'opacity-0'"
                decoding="async"
            >
        </span>
        <span class="relative block lg:hidden" style="width: 36px; height: 36px;">
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
        @if(in_array($resolvedLayout, ['horizontal', 'compact', 'auto'], true))
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                width="{{ max($styleWidth, $minWidth) }}"
                height="{{ $heightPx }}"
                class="hidden lg:block h-10 w-auto max-w-[min(100%,200px)] object-contain object-left"
                style="min-width: {{ $minWidth }}px;"
                decoding="async"
            >
            <img
                src="{{ $mobileSrc }}"
                alt="{{ $alt }}"
                width="36"
                height="36"
                class="block lg:hidden h-9 w-9 object-contain"
                decoding="async"
            >
        @else
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                width="{{ max($styleWidth, $minWidth) }}"
                height="{{ $heightPx }}"
                class="w-auto object-contain object-left"
                style="min-width: {{ $minWidth }}px; height: {{ $heightPx }}px;"
                decoding="async"
            >
        @endif
    @endif

    @if($showTagline)
        <span class="font-[family-name:var(--font-display)] text-sm italic font-normal text-current/80">{{ BrandAssets::TAGLINE }}</span>
    @endif
</span>
