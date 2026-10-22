@props(['data' => []])

@php
    $hero = $data['hero'] ?? [];
    $featured = $hero['featured'] ?? [];
    $enquireHref = route('contact', ['type' => 'hospitality']);
    $featuredHref = $featured['href']
        ?? route('divisions.hospitality', array_filter([
            'destination' => $featured['search'] ?? 'Blue Area',
            'guests' => 2,
        ]));
@endphp

<section class="mh-hero" aria-label="Maskanoor Hotels">
    <div class="mh-hero__media" aria-hidden="true">
        <img
            src="{{ $hero['background'] ?? '/images/listings/hotel-1.jpg' }}"
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
                <b>Maskanoor Hotels</b>
            </p>
            <h1>Considered stays,<br>wherever you travel.</h1>
            <p class="mh-hero__lead">Boutique hotels and short-stay residences, presented with care.</p>
            <div class="mh-hero__actions">
                <a href="#stays" class="mg-btn mg-btn--gold">Browse stays</a>
                <a href="{{ $enquireHref }}" class="mg-btn mg-btn--ghost">Enquire for groups</a>
            </div>
        </div>

        <a href="{{ $featuredHref }}" class="mh-feat-card" aria-label="View {{ $featured['title'] ?? 'featured stay' }}">
            <div class="mh-feat-card__media">
                <img
                    src="{{ $featured['image'] ?? '/images/home/hero-1.jpg' }}"
                    alt="{{ $featured['image_alt'] ?? '' }}"
                    width="720"
                    height="500"
                    loading="lazy"
                    decoding="async"
                >
                <span class="mh-tag mh-tag--gold">{{ $featured['tag'] ?? 'Guest favourite' }}</span>
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
