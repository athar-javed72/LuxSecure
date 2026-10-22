@props(['destinations' => []])

<section class="mg-section mh-destinations" aria-labelledby="mh-dest-heading">
    <div class="mg-container">
        <h2 class="mg-h2" id="mh-dest-heading">Where to look first</h2>
        <div class="mh-city-grid">
            @foreach($destinations as $city)
                @php
                    $href = ! empty($city['browse_all'])
                        ? route('divisions.hospitality', ['guests' => 2])
                        : route('divisions.hospitality', array_filter([
                            'destination' => $city['search'] ?? null,
                            'guests' => 2,
                        ]));
                @endphp
                <a href="{{ $href }}" class="mh-city-card" aria-label="{{ $city['name'] }} — {{ $city['line'] }}">
                    <img
                        src="{{ $city['image'] }}"
                        alt="{{ $city['image_alt'] }}"
                        width="800"
                        height="1000"
                        loading="lazy"
                        decoding="async"
                    >
                    <div class="mh-city-card__shade" aria-hidden="true"></div>
                    <div class="mh-city-card__body">
                        <h3>{{ $city['name'] }}</h3>
                        <span>{{ $city['line'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
