@php
    $cards = [
        [
            'title' => 'Hotels',
            'body' => 'Considered guest experiences, built around comfort, character and thoughtful service.',
            'href' => route('divisions.hospitality'),
            'label' => 'Discover Hotels →',
            'image' => '/images/divisions/hotels.jpg',
            'alt' => 'Luxury hotel suite with velvet seating and soft lighting',
        ],
        [
            'title' => 'Properties',
            'body' => 'Ownership, leasing and management opportunities, presented clearly.',
            'href' => route('properties'),
            'label' => 'Discover Properties →',
            'image' => '/images/divisions/properties.jpg',
            'alt' => 'Modern residence exterior at dusk',
        ],
        [
            'title' => 'Living',
            'body' => 'Refined residences for stays that feel effortless, short-term or extended.',
            'href' => route('divisions.living'),
            'label' => 'Discover Living →',
            'image' => '/images/divisions/living.jpg',
            'alt' => 'Minimalist living room corner with mustard armchair',
        ],
    ];
@endphp

<section class="mg-section mg-divisions" id="about" aria-labelledby="mg-divisions-heading">
    <div class="mg-container">
        <p class="mg-eyebrow">Our divisions</p>
        <h2 class="mg-h2" id="mg-divisions-heading">Three ways to experience Maskanoor</h2>
        <div class="mg-divisions__grid">
            @foreach($cards as $card)
                <a href="{{ $card['href'] }}" class="mg-division-card" aria-label="{{ $card['title'] }} — {{ $card['label'] }}">
                    <img src="{{ $card['image'] }}" alt="{{ $card['alt'] }}" width="800" height="1200" loading="lazy" decoding="async">
                    <div class="mg-division-card__shade" aria-hidden="true"></div>
                    <div class="mg-division-card__body">
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                        <span class="mg-division-card__link">{{ $card['label'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
