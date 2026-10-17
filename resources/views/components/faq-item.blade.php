@props([
    'question',
    'answer',
    'open' => false,
])

@php
    $faqConfig = ['open' => (bool) $open];
@endphp

<div
    x-data="faqItem"
    data-alpine-config="{{ json_encode($faqConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    {{ $attributes->merge(['class' => 'rounded-sm border border-slate-200 px-5 py-4']) }}
>
    <button
        type="button"
        class="flex w-full items-center justify-between gap-4 text-left min-h-11 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand"
        @click="toggle()"
        :aria-expanded="ariaExpanded()"
    >
        <h3 class="text-base font-semibold text-fs-primary">{{ $question }}</h3>
        <svg
            class="w-5 h-5 shrink-0 text-fs-secondary transition-transform duration-150"
            :class="chevronClass()"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="open" x-cloak class="mt-3 text-sm text-fs-secondary">
        {{ $answer }}
    </div>
</div>
