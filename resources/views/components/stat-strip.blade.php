@props([
    'stats' => [],
    'flushTop' => false,
])

@php
    $stats = collect($stats)->filter(fn ($s) => ($s['value'] ?? 0) > 0)->values();
@endphp

@if($stats->count() >= 2)
    <div {{ $attributes->class([
        'fs-stat-strip bg-[var(--color-ivory-50)]',
        $flushTop ? 'border-b border-[var(--color-border)]' : 'border-y border-[var(--color-border)]',
    ]) }}>
        <x-container class="py-6 md:py-8">
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-5 md:gap-6 text-center max-w-3xl mx-auto">
                @foreach($stats as $stat)
                    <div data-reveal>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">{{ $stat['label'] }}</dt>
                        <dd class="mt-1.5 font-[family-name:var(--font-display)] text-3xl md:text-4xl font-semibold text-[var(--color-forest)] fs-numeral" data-count-up="{{ (int) $stat['value'] }}">{{ number_format((int) $stat['value']) }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-container>
    </div>
@endif
