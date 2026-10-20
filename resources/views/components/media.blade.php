@props([
    'slot' => null,
    'index' => 0,
    'media' => null,
    'sizes' => '100vw',
    'eager' => false,
    'class' => '',
    'fallbackPurpose' => null,
    'fallbackTypes' => null,
])

@php
    /** @var \App\Services\MediaResolver $resolver */
    $resolver = app(\App\Services\MediaResolver::class);
    $item = $media;
    if (! $item && $slot) {
        $item = $resolver->resolve($slot, (int) $index, function () use ($resolver, $fallbackPurpose, $fallbackTypes) {
            return $resolver->listingCoverFallback($fallbackPurpose, $fallbackTypes);
        });
    }
    $item = $item ?: $resolver->brandedFallback();
    $isBranded = ($item['kind'] ?? '') === 'branded' || empty($item['src']);
    $objectPosition = $item['object_position'] ?? '50% 50%';
    $alt = $item['decorative'] ?? false ? '' : ($item['alt'] ?? '');
    $w = (int) ($item['width'] ?? 1200);
    $h = (int) ($item['height'] ?? 1500);
    $variants = $item['variants'] ?? [];
@endphp

@if($isBranded)
    <div {{ $attributes->class(['fs-media-branded relative overflow-hidden bg-[var(--color-forest)]', $class]) }} aria-hidden="true">
        <img src="{{ $item['monogram'] ?? \App\Support\BrandAssets::logo('monogram-nostar', 'dark') }}" alt="" width="160" height="160" class="absolute inset-0 m-auto w-[28%] max-w-[180px] opacity-[0.06] pointer-events-none" decoding="async">
        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[rgba(13,29,25,.55)] to-transparent"></div>
    </div>
@elseif(($item['type'] ?? '') === 'video' && !empty($item['src']))
    <div {{ $attributes->class(['relative overflow-hidden', $class]) }} data-hero-video-wrap>
        <video
            class="absolute inset-0 w-full h-full object-cover hidden lg:block"
            style="object-position: {{ $objectPosition }}"
            muted loop playsinline preload="metadata"
            poster="{{ $item['poster'] ?? '' }}"
            aria-hidden="true"
            data-bg-video
        >
            <source src="{{ $item['src'] }}" type="video/mp4">
        </video>
        @if(!empty($item['poster']))
            <img src="{{ $item['poster'] }}" alt="{{ $alt }}" width="{{ $w }}" height="{{ $h }}" class="absolute inset-0 w-full h-full object-cover lg:hidden" style="object-position: {{ $objectPosition }}" @if($eager) fetchpriority="high" @else loading="lazy" @endif decoding="async">
        @endif
        <button type="button" class="absolute bottom-4 right-4 z-10 h-11 px-3 rounded-[var(--radius-button)] text-xs font-semibold bg-[rgba(13,29,25,.55)] text-[var(--color-ivory)] border border-white/20" data-bg-video-toggle aria-pressed="false">Pause</button>
    </div>
@else
    <picture {{ $attributes->class([$class]) }}>
        @foreach(['avif', 'webp'] as $ext)
            @if(!empty($variants[$ext]))
                @php
                    $srcset = collect($variants[$ext])->map(fn ($url, $width) => $url.' '.$width.'w')->implode(', ');
                @endphp
                <source type="image/{{ $ext }}" srcset="{{ $srcset }}" sizes="{{ $sizes }}">
            @endif
        @endforeach
        <img
            src="{{ $item['src'] }}"
            alt="{{ $alt }}"
            width="{{ $w }}"
            height="{{ $h }}"
            decoding="async"
            @if($eager) fetchpriority="high" @else loading="lazy" @endif
            class="w-full h-full object-cover"
            style="object-position: {{ $objectPosition }}"
        >
    </picture>
@endif
