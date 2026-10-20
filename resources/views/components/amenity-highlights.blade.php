@props(['amenities' => []])

@php $amenities = collect($amenities); @endphp

@if($amenities->count() >= 3)
    <div {{ $attributes->class(['grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4']) }}>
        @foreach($amenities as $item)
            <a href="{{ $item['href'] }}" class="flex items-start gap-3 p-4 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white hover:border-[color-mix(in_srgb,var(--color-gold-dark)_35%,transparent)] transition-colors">
                <span class="inline-flex text-[var(--color-forest)] shrink-0" aria-hidden="true">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $item['icon'] }}"/></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-[var(--color-ink)]">{{ $item['label'] }}</span>
                    <span class="block text-xs text-[var(--color-muted)] mt-0.5 fs-numeral">{{ $item['count'] }} listings</span>
                </span>
            </a>
        @endforeach
    </div>
@endif
