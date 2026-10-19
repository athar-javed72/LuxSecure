@props([
    'question',
    'answer',
    'open' => false,
])

<div
    data-faq
    {{ $attributes->merge(['class' => 'rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white px-5 py-4']) }}
>
    <button
        type="button"
        data-faq-toggle
        class="flex w-full items-center justify-between gap-4 text-left min-h-11 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
    >
        <h3 class="text-base font-semibold text-[var(--color-ink)]">{{ $question }}</h3>
        <svg
            data-faq-chevron
            class="fs-faq-chevron w-5 h-5 shrink-0 text-[var(--color-muted)] {{ $open ? 'is-open' : '' }}"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div data-faq-panel class="fs-faq-panel {{ $open ? 'is-open' : '' }}">
        <div>
            <p class="pt-3 text-[15px] md:text-base leading-relaxed text-[var(--color-muted-strong)] max-w-prose">
                {{ $answer }}
            </p>
        </div>
    </div>
</div>
