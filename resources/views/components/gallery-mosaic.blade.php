@props(['items' => []])

@php
    $items = array_values($items);
    $count = count($items);
@endphp

@if($count > 0)
    <div
        {{ $attributes->class(['fs-gallery-mosaic']) }}
        x-data="galleryLightbox"
        data-alpine-config="{{ json_encode(['items' => collect($items)->map(fn ($i) => [
            'src' => $i['src'] ?? '',
            'alt' => $i['alt'] ?? '',
        ])->values()], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    >
        <div class="@if($count === 3) grid md:grid-cols-2 gap-3 @elseif($count === 4) grid sm:grid-cols-2 gap-3 @else grid sm:grid-cols-2 lg:grid-cols-3 gap-3 @endif">
            @foreach($items as $i => $item)
                <button
                    type="button"
                    class="relative overflow-hidden rounded-[var(--radius-card)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]
                        @if($count === 3 && $i === 0) md:row-span-2 min-h-[280px] md:min-h-full @elseif($count >= 5 && $i === 0) lg:col-span-2 lg:row-span-2 min-h-[240px] @else aspect-[4/3] @endif"
                    :data-index="String({{ $i }})"
                    @click="openAtEvent($event)"
                    aria-label="Open gallery image {{ $i + 1 }}"
                >
                    <img src="{{ $item['src'] }}" alt="{{ $item['alt'] ?? '' }}" width="1600" height="1200" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover" style="object-position: {{ $item['object_position'] ?? '50% 50%' }}">
                </button>
            @endforeach
        </div>

        <div
            x-cloak
            x-show="open"
            class="fixed inset-0 z-[210] flex items-center justify-center bg-[rgba(13,29,25,.88)] p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Image gallery"
            @keydown.escape.window="closeLightbox()"
        >
            <button type="button" class="absolute top-4 right-4 h-11 w-11 rounded-full border border-white/30 text-[var(--color-ivory)]" @click="closeLightbox()" aria-label="Close">×</button>
            <button type="button" class="absolute left-4 h-11 w-11 rounded-full border border-white/30 text-[var(--color-ivory)]" @click="prev()" aria-label="Previous">‹</button>
            <figure class="max-w-5xl w-full">
                <img :src="currentSrc()" :alt="currentAlt()" class="w-full max-h-[80vh] object-contain mx-auto" width="1600" height="1200">
                <figcaption class="mt-3 text-center text-sm text-[var(--color-sage)]">
                    <span x-text="counterLabel()"></span>
                </figcaption>
            </figure>
            <button type="button" class="absolute right-4 h-11 w-11 rounded-full border border-white/30 text-[var(--color-ivory)]" @click="next()" aria-label="Next">›</button>
        </div>
    </div>
@endif
