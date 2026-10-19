@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'subtitle' => null,
    'headingId' => null,
    'actionHref' => null,
    'actionLabel' => 'View all',
    'onDark' => false,
    'controls' => null,
])

@php
    $leadText = $lead ?? $subtitle;
    $titleClass = $onDark ? 'text-[var(--color-ivory)]' : 'text-[var(--color-ink)]';
    $leadClass = $onDark ? 'text-[var(--color-sage)]' : 'text-[var(--color-muted)]';
    $actionClass = $onDark ? 'fs-view-all !text-[var(--color-gold)]' : 'fs-view-all';
@endphp

<div {{ $attributes->class(['w-full']) }} data-reveal>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
        <div class="min-w-0 text-left flex-1">
            @if($eyebrow)
                <x-eyebrow :on-dark="$onDark">{{ $eyebrow }}</x-eyebrow>
            @endif
            <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2 {{ $eyebrow ? 'mt-4' : '' }}">
                <h2
                    @if($headingId) id="{{ $headingId }}" @endif
                    class="fs-display {{ $titleClass }}"
                >
                    {{ $title }}
                </h2>
                <div class="flex items-center gap-2 shrink-0">
                    {{ $controls }}
                    @if($actionHref)
                        <a
                            href="{{ $actionHref }}"
                            class="{{ $actionClass }} focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
                        >
                            {{ $actionLabel }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            @if($leadText)
                <p class="mt-3 fs-lead {{ $leadClass }}">{{ $leadText }}</p>
            @endif
        </div>
    </div>
</div>
