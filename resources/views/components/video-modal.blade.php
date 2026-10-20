@props([
    'media' => null,
    'caption' => null,
])

@php
    $media = $media ?: null;
    $has = $media && (($media['type'] ?? '') === 'video' || ($media['type'] ?? '') === 'embed' || !empty($media['embed_url']) || !empty($media['src']));
    $poster = $media['poster'] ?? null;
    $isEmbed = ($media['type'] ?? '') === 'embed' || !empty($media['embed_url']);
@endphp

@if($has && $poster)
    <div
        {{ $attributes->class(['relative']) }}
        x-data="videoModal"
        data-alpine-config="{{ json_encode([
            'src' => $isEmbed ? ($media['embed_url'] ?? '') : ($media['src'] ?? ''),
            'embed' => $isEmbed,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    >
        <button type="button" class="group relative block w-full aspect-video overflow-hidden rounded-[var(--radius-card)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]" @click="openModal()" aria-haspopup="dialog">
            <img src="{{ $poster }}" alt="{{ $media['alt'] ?? '' }}" width="1920" height="1080" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover">
            <span class="absolute inset-0 bg-[rgba(13,29,25,.28)]"></span>
            <span class="absolute inset-0 m-auto h-14 w-14 rounded-full bg-[var(--color-gold)] text-[var(--color-forest)] flex items-center justify-center shadow-lg" aria-hidden="true">
                <svg class="w-6 h-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </span>
            <span class="sr-only">Play video</span>
        </button>
        @if($caption || !empty($media['caption']))
            <p class="mt-3 text-sm text-[var(--color-muted)]">{{ $caption ?? $media['caption'] }}</p>
        @endif

        <div
            x-cloak
            x-show="open"
            class="fixed inset-0 z-[210] flex items-center justify-center bg-[rgba(13,29,25,.9)] p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Video"
            @keydown.escape.window="closeModal()"
        >
            <button type="button" class="absolute top-4 right-4 h-11 w-11 rounded-full border border-white/30 text-[var(--color-ivory)]" @click="closeModal()" aria-label="Close">×</button>
            <div class="w-full max-w-4xl aspect-video bg-black rounded-[var(--radius-card)] overflow-hidden" x-ref="playerHost">
                <template x-if="open && embed">
                    <iframe class="w-full h-full" :src="src" title="Video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </template>
                <template x-if="open && !embed">
                    <video class="w-full h-full" :src="src" controls playsinline preload="none"></video>
                </template>
            </div>
        </div>
    </div>
@endif
