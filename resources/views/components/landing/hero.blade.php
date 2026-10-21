@props([
    'heroImage' => '/images/home/hero-2.jpg',
    'featured' => null,
    'featuredImage' => '/images/home/hero-1.jpg',
])

@php
    $featuredUrl = null;
    $featuredTitle = 'Blue Area Boutique Hotel';
    $featuredPrice = 'PKR 25,000 /night';
    $featuredMeta = 'Blue Area, Islamabad · 2 room types';
    $featuredSrc = $featuredImage;

    if ($featured) {
        $featuredUrl = route('properties.show', $featured);
        $featuredTitle = $featured->title;
        $featuredSrc = $featured->primary_image_url ?: $featuredImage;
        $amount = \App\Support\Money::formatCompact($featured->price, $featured->currency);
        $suffix = match (true) {
            $featured->purpose === 'short_stay' || $featured->price_unit === 'per_night' => ' /night',
            $featured->price_unit === 'per_month' || $featured->purpose === 'rent' => ' /month',
            default => '',
        };
        $featuredPrice = $amount.$suffix;
        $featuredMeta = $featured->location;
        if ($featured->bedrooms) {
            $featuredMeta .= ' · '.$featured->bedrooms.' beds';
        }
    }
@endphp

<section class="mg-hero" aria-label="Hero">
    <div class="mg-hero__media" aria-hidden="true">
        <img
            src="{{ $heroImage }}"
            alt=""
            width="1920"
            height="1080"
            fetchpriority="high"
            decoding="async"
        >
        <div class="mg-hero__overlay"></div>
    </div>

    <div class="mg-container mg-hero__inner">
        <div>
            <p class="mg-eyebrow mg-hero__eyebrow">Hospitality · Property · Living</p>
            <h1>Elevating Spaces. Enriching Stays.</h1>
            <p class="mg-hero__lead">Hotels, residences and carefully selected properties across six countries, presented with care.</p>
            <div class="mg-hero__actions">
                <a href="{{ route('properties') }}" class="mg-btn mg-btn--gold">Explore properties</a>
                <a href="{{ route('properties', ['purpose' => 'short_stay']) }}" class="mg-btn mg-btn--ghost">Book a stay</a>
            </div>
        </div>

        @if($featuredUrl)
            <a href="{{ $featuredUrl }}" class="mg-hero__card" aria-label="View {{ $featuredTitle }}">
                <div class="mg-hero__card-media">
                    <img
                        src="{{ $featuredSrc }}"
                        alt="{{ $featuredTitle }}"
                        width="720"
                        height="500"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="mg-hero__card-tag">Featured stay</span>
                </div>
                <div class="mg-hero__card-body">
                    <p class="mg-hero__card-price">{{ $featuredPrice }}</p>
                    <p class="mg-hero__card-title">{{ $featuredTitle }}</p>
                    <p class="mg-hero__card-meta">{{ $featuredMeta }}</p>
                </div>
            </a>
        @else
            <aside class="mg-hero__card" aria-label="Featured stay">
                <div class="mg-hero__card-media">
                    <img
                        src="{{ $featuredSrc }}"
                        alt="{{ $featuredTitle }}"
                        width="720"
                        height="500"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="mg-hero__card-tag">Featured stay</span>
                </div>
                <div class="mg-hero__card-body">
                    <p class="mg-hero__card-price">{{ $featuredPrice }}</p>
                    <p class="mg-hero__card-title">{{ $featuredTitle }}</p>
                    <p class="mg-hero__card-meta">{{ $featuredMeta }}</p>
                </div>
            </aside>
        @endif
    </div>
</section>
