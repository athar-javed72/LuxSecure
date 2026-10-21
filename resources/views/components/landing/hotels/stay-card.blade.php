@props(['stay' => []])

@php
    $listing = $stay['listing'] ?? null;
    $listingId = $stay['listing_id'] ?? ($listing->id ?? null);
    $title = $stay['title'] ?? 'stay';
    $searchTerm = $stay['search'] ?? null;

    if (! empty($stay['href'])) {
        $href = $stay['href'];
    } elseif ($listing) {
        $href = route('properties.show', $listing);
    } else {
        $href = route('divisions.hospitality', array_filter([
            'destination' => $searchTerm,
        ]));
    }

    $showFrom = array_key_exists('show_from', $stay) ? (bool) $stay['show_from'] : true;
    $isFavorited = (bool) ($stay['is_favorited'] ?? false);
    $loginRedirect = request()->getRequestUri();
@endphp

<article class="mh-stay-card">
    <a href="{{ $href }}" class="mh-stay-card__link" aria-label="View {{ $title }}">
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
            @if($showFrom)
                <p class="mh-stay-card__from">From</p>
            @endif
            <p class="mh-price">
                {{ $stay['price'] ?? '' }}
                <small>{{ $stay['price_suffix'] ?? '' }}</small>
            </p>
            @if(! empty($stay['total_label']))
                <p class="mh-stay-card__total">{{ $stay['total_label'] }}</p>
            @endif
            <h3 class="mh-stay-card__title">{{ $title }}</h3>
            <p class="mh-stay-card__loc">{{ $stay['location'] ?? '' }}</p>
            @if(! empty($stay['amenities']))
                <div class="mh-stay-card__amen">
                    @foreach($stay['amenities'] as $amenity)
                        <span>{{ $amenity }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </a>

    @if($listing)
        @auth
            <form
                action="{{ route('favorites.toggle', $listing) }}"
                method="POST"
                class="mh-stay-card__save-form"
            >
                @csrf
                <button
                    type="submit"
                    class="mh-stay-card__save {{ $isFavorited ? 'is-saved' : '' }}"
                    aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                    aria-label="{{ $isFavorited ? 'Remove from saved' : 'Save '.$title }}"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 11c0 5.6-7 10-7 10z"/>
                    </svg>
                </button>
            </form>
        @else
            <a
                href="{{ route('login.form', ['redirect' => $loginRedirect]) }}"
                class="mh-stay-card__save"
                aria-label="Sign in to save {{ $title }}"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 11c0 5.6-7 10-7 10z"/>
                </svg>
            </a>
        @endauth
    @endif
</article>
