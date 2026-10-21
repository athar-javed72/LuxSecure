@props(['stay' => []])

@php
    $href = route('properties', array_filter([
        'purpose' => 'short_stay',
        'search' => $stay['search'] ?? null,
    ]));
@endphp

<article class="mh-stay-card">
    <a href="{{ $href }}" class="mh-stay-card__link" aria-label="View {{ $stay['title'] ?? 'stay' }}">
        <div class="mh-stay-card__media">
            <img
                src="{{ $stay['image'] ?? '' }}"
                alt="{{ $stay['image_alt'] ?? '' }}"
                width="800"
                height="600"
                loading="lazy"
                decoding="async"
            >
            <span class="mh-tag">Short stay</span>
        </div>
        <div class="mh-stay-card__body">
            <p class="mh-stay-card__from">From</p>
            <p class="mh-price">
                {{ $stay['price'] ?? '' }}
                <small>{{ $stay['price_suffix'] ?? '' }}</small>
            </p>
            <h3 class="mh-stay-card__title">{{ $stay['title'] ?? '' }}</h3>
            <p class="mh-stay-card__loc">{{ $stay['location'] ?? '' }}</p>
            @if(!empty($stay['amenities']))
                <div class="mh-stay-card__amen">
                    @foreach($stay['amenities'] as $amenity)
                        <span>{{ $amenity }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </a>
    <button
        type="button"
        class="mh-stay-card__save"
        data-mg-save
        aria-pressed="false"
        aria-label="Save {{ $stay['title'] ?? 'stay' }}"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 11c0 5.6-7 10-7 10z"/>
        </svg>
    </button>
</article>
