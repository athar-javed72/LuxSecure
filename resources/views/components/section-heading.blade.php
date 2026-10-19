@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'subtitle' => null,
    'headingId' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'onDark' => false,
])

@php
    $leadText = $lead ?? $subtitle;
    $titleClass = $onDark ? 'text-[var(--color-ivory)]' : 'text-[var(--color-ink)]';
    $leadClass = $onDark ? 'text-[var(--color-sage)]' : 'text-[var(--color-muted)]';
    $actionClass = $onDark
        ? 'text-[var(--color-gold)] hover:underline'
        : 'text-[var(--color-gold-dark)] hover:underline';
@endphp

<div {{ $attributes->class(['w-full']) }}>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between sm:gap-6">
        <div class="min-w-0 text-left">
            @if($eyebrow)
                <p class="{{ $onDark ? 'fs-eyebrow-on-dark' : 'fs-eyebrow' }}">{{ $eyebrow }}</p>
            @endif
            <h2
                @if($headingId) id="{{ $headingId }}" @endif
                class="fs-display {{ $titleClass }} {{ $eyebrow ? 'mt-4' : '' }}"
            >
                {{ $title }}
            </h2>
            @if($leadText)
                <p class="mt-3 fs-lead {{ $leadClass }}">{{ $leadText }}</p>
            @endif
        </div>
        @if($actionHref && $actionLabel)
            <a
                href="{{ $actionHref }}"
                class="shrink-0 inline-flex items-center min-h-11 text-sm font-semibold tracking-wide {{ $actionClass }} focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
            >
                {{ $actionLabel }}
            </a>
        @endif
    </div>
</div>
