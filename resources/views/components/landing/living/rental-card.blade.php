@props(['rental' => []])

@php
    $href = route('properties', array_filter([
        'purpose' => 'rent',
        'search' => $rental['search'] ?? null,
    ]));
    $specs = collect([
        $rental['beds'] ?? null,
        $rental['baths'] ?? null,
        $rental['area'] ?? null,
    ])->filter()->values();
@endphp

<article class="ml-rental-card">
    <a href="{{ $href }}" class="ml-rental-card__link" aria-label="View {{ $rental['title'] ?? 'rental' }}">
        <div class="ml-rental-card__media">
            <img
                src="{{ $rental['image'] ?? '' }}"
                alt="{{ $rental['image_alt'] ?? '' }}"
                width="640"
                height="480"
                loading="lazy"
                decoding="async"
            >
            <span class="mh-tag">For rent</span>
        </div>
        <div class="ml-rental-card__body">
            <p class="mh-price">
                {{ $rental['price'] ?? '' }}
                <small>{{ $rental['price_suffix'] ?? '' }}</small>
            </p>
            <h3 class="ml-rental-card__title">{{ $rental['title'] ?? '' }}</h3>
            <p class="ml-rental-card__loc">{{ $rental['location'] ?? '' }}</p>
            @if($specs->isNotEmpty())
                <div class="ml-rental-card__specs">
                    @foreach($specs as $spec)
                        <span>{{ $spec }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </a>
    <button
        type="button"
        class="ml-rental-card__save"
        data-mg-save
        aria-pressed="false"
        aria-label="Save {{ $rental['title'] ?? 'rental' }}"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 11c0 5.6-7 10-7 10z"/>
        </svg>
    </button>
</article>
