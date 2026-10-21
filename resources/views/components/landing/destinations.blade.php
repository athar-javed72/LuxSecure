@props([
    'destinations' => null,
])

@php
    $fromDb = collect($destinations ?? [])->filter()->values();
    $staticFallback = [
        ['name' => 'Islamabad', 'line' => 'Explore stays', 'image' => '/images/home/about.jpg', 'href' => route('properties', ['search' => 'Islamabad']), 'alt' => 'Urban glass towers against an overcast sky'],
        ['name' => 'Dubai', 'line' => 'Explore stays', 'image' => '/images/home/break.jpg', 'href' => route('properties', ['search' => 'Dubai']), 'alt' => 'Architectural exterior with warm evening light'],
        ['name' => 'Kuala Lumpur', 'line' => 'Explore stays', 'image' => '/images/listings/house-1.jpg', 'href' => route('properties', ['search' => 'Kuala Lumpur']), 'alt' => 'Contemporary house facade with greenery'],
        ['name' => 'Karachi', 'line' => 'Explore rentals', 'image' => '/images/listings/plot-1.jpg', 'href' => route('properties', ['search' => 'Karachi']), 'alt' => 'Open plot and low-rise urban edge'],
        ['name' => 'Lahore', 'line' => 'Explore sales', 'image' => '/images/home/hero-3.jpg', 'href' => route('properties', ['search' => 'Lahore']), 'alt' => 'Sunlit residential living room'],
        ['name' => 'London', 'line' => 'Explore stays', 'image' => '/images/home/break-mobile.jpg', 'href' => route('properties', ['search' => 'London']), 'alt' => 'Architectural detail in soft daylight'],
    ];

    $cards = $fromDb->isNotEmpty()
        ? $fromDb->take(6)->map(function ($dest) {
            return [
                'name' => $dest['city']->name,
                'line' => $dest['label'],
                'image' => $dest['cover'] ?: '/images/home/about.jpg',
                'href' => $dest['href'],
                'alt' => $dest['city']->name.' listings',
                'focal' => $dest['focal'] ?? '50% 50%',
            ];
        })
        : collect($staticFallback);
@endphp

<section class="mg-section mg-destinations" aria-labelledby="mg-dest-heading">
    <div class="mg-container">
        <p class="mg-eyebrow">Destinations</p>
        <h2 class="mg-h2" id="mg-dest-heading">Places with something to explore</h2>
        <div class="mg-dest-grid">
            @foreach($cards as $city)
                <a href="{{ $city['href'] }}" class="mg-dest-card" aria-label="Browse listings in {{ $city['name'] }}">
                    <img
                        src="{{ $city['image'] }}"
                        alt="{{ $city['alt'] }}"
                        width="800"
                        height="1000"
                        loading="lazy"
                        decoding="async"
                        @if(!empty($city['focal'])) style="object-position: {{ $city['focal'] }}" @endif
                    >
                    <div class="mg-dest-card__shade" aria-hidden="true"></div>
                    <div class="mg-dest-card__body">
                        <h3>{{ $city['name'] }}</h3>
                        <p>{{ $city['line'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
