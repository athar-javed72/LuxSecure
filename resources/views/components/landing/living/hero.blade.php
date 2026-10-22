@props(['data' => []])

@php
    $hero = $data['hero'] ?? [];
    $featured = $hero['featured'] ?? [];
    $featuredHref = route('properties', array_filter([
        'purpose' => 'rent',
        'search' => $featured['search'] ?? null,
    ]));
@endphp

<section class="mh-hero ml-hero" aria-label="Maskanoor Living">
    <div class="mh-hero__media" aria-hidden="true">
        <img
            src="{{ $hero['background'] ?? '/images/listings/house-1.jpg' }}"
            alt=""
            width="1920"
            height="1080"
            fetchpriority="high"
            decoding="async"
        >
        <div class="mh-hero__overlay"></div>
    </div>

    <div class="mg-container mh-hero__inner">
        <div>
            <p class="mh-crumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true"> / </span>
                <b>Maskanoor Living</b>
            </p>
            <h1>Homes that feel settled<br>from day one.</h1>
            <p class="mh-hero__lead">Rentals and residences for everyday life: clear, calm and carefully presented.</p>
            <div class="mh-hero__actions">
                <a href="#rentals" class="mg-btn mg-btn--gold">Browse rentals</a>
                <a href="#landlords" class="mg-btn mg-btn--ghost">I am a landlord</a>
            </div>
        </div>

        <a href="{{ $featuredHref }}" class="mh-feat-card" aria-label="View {{ $featured['title'] ?? 'featured rental' }}">
            <div class="mh-feat-card__media">
                <img
                    src="{{ $featured['image'] ?? '/images/listings/apartment-2.jpg' }}"
                    alt="{{ $featured['image_alt'] ?? '' }}"
                    width="720"
                    height="500"
                    loading="lazy"
                    decoding="async"
                >
                <span class="mh-tag">{{ $featured['tag'] ?? 'For rent' }}</span>
            </div>
            <div class="mh-feat-card__body">
                <p class="mh-price">
                    {{ $featured['price'] ?? '' }}
                    <small>{{ $featured['price_suffix'] ?? '' }}</small>
                </p>
                <p class="mh-feat-card__title">{{ $featured['title'] ?? '' }}</p>
                <p class="mh-feat-card__meta">{{ $featured['meta'] ?? '' }}</p>
            </div>
        </a>
    </div>
</section>
