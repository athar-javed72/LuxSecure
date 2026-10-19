@props([
    'eyebrow' => null,
    'title' => "Let's shape what comes next.",
    'lead' => 'Tell us whether you are exploring a stay, a property, or a potential partnership.',
    'primaryLabel' => 'Start a Conversation',
    'primaryHref' => null,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'rows' => [], // [['title','copy','href'], ...]
])

@php
    $primaryHref = $primaryHref ?? route('contact', ['type' => 'general']);
    $eyebrow = $eyebrow ?? ($siteName ?? config('app.name'));
@endphp

<section {{ $attributes->class(['relative z-0 fs-section-ivory']) }}>
    <x-container>
        <div class="relative overflow-hidden rounded-[var(--radius-card)] bg-[var(--color-forest-shadow)] px-6 py-12 md:px-12 md:py-14 border-t border-[color-mix(in_srgb,var(--color-gold)_40%,transparent)]" data-reveal>
            <img src="{{ \App\Support\BrandAssets::logo('monogram-nostar', 'dark') }}" alt="" aria-hidden="true" class="pointer-events-none absolute -bottom-8 -right-6 w-56 md:w-72 opacity-[0.07] select-none">
            <div class="relative grid {{ !empty($rows) ? 'lg:grid-cols-2 gap-10 lg:gap-16' : '' }} items-start">
                <div>
                    <x-eyebrow on-dark>{{ $eyebrow }}</x-eyebrow>
                    <h2 class="mt-4 text-[var(--color-ivory)]">{{ $title }}</h2>
                    @if($lead)
                        <p class="mt-3 text-[var(--color-sage)] max-w-[52ch]" style="text-wrap: pretty;">{{ $lead }}</p>
                    @endif
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <x-button variant="primary" :href="$primaryHref" surface="dark" class="fs-btn-motion">{{ $primaryLabel }}</x-button>
                        @if($secondaryLabel && $secondaryHref)
                            <x-button variant="secondary" :href="$secondaryHref" surface="dark" class="fs-btn-motion">{{ $secondaryLabel }}</x-button>
                        @endif
                    </div>
                </div>
                @if(!empty($rows))
                    <div class="space-y-0">
                        @foreach($rows as $row)
                            <a href="{{ $row['href'] }}" class="flex items-center justify-between gap-4 py-4 border-b border-[color-mix(in_srgb,var(--color-ivory)_12%,transparent)] last:border-b-0 group focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)]">
                                <span>
                                    <span class="block text-[var(--color-ivory)] font-semibold">{{ $row['title'] }}</span>
                                    <span class="block mt-1 text-sm text-[var(--color-sage)]">{{ $row['copy'] }}</span>
                                </span>
                                <span class="text-[var(--color-gold)] transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true">→</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </x-container>
</section>
