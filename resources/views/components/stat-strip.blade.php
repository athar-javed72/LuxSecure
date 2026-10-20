@props(['stats' => []])

@php
    $stats = collect($stats)->filter(fn ($s) => ($s['value'] ?? 0) > 0)->values();
@endphp

@if($stats->count() >= 2)
    <div {{ $attributes->class(['fs-stat-strip border-y border-[var(--color-border)] bg-[var(--color-ivory-50)]']) }}>
        <x-container class="py-8 md:py-10">
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-6 text-center">
                @foreach($stats as $stat)
                    <div data-reveal>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">{{ $stat['label'] }}</dt>
                        <dd class="mt-2 font-[family-name:var(--font-display)] text-3xl md:text-4xl font-semibold text-[var(--color-forest)] fs-numeral" data-count-up="{{ (int) $stat['value'] }}">{{ number_format((int) $stat['value']) }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-container>
    </div>
@endif
