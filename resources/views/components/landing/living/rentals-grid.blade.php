@props([
    'rentals' => [],
    'chips' => [],
    'request' => [],
])

@php
    $rentals = collect($rentals)->filter(fn ($r) => ! empty($r['title']) && ! empty($r['price']))->values();
    $cols = $rentals->count() >= 2 ? 'ml-rentals-grid--multi' : 'ml-rentals-grid--single';
@endphp

<section class="mg-section ml-rentals" id="rentals" aria-labelledby="ml-rentals-heading">
    <div class="mg-container">
        <div class="ml-rentals__head">
            <h2 class="mg-h2" id="ml-rentals-heading">Featured rentals</h2>
            @if(!empty($chips))
                <div class="ml-chips" aria-label="Cities">
                    @foreach($chips as $chip)
                        @if(!empty($chip['search']))
                            <a
                                href="{{ route('properties', ['purpose' => 'rent', 'search' => $chip['search']]) }}"
                                @class(['ml-chip', 'is-active' => !empty($chip['active'])])
                            >{{ $chip['label'] }}</a>
                        @else
                            <span @class(['ml-chip', 'ml-chip--soon', 'is-active' => !empty($chip['active'])])>{{ $chip['label'] }}</span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="ml-rentals-grid {{ $cols }}">
            @foreach($rentals as $rental)
                <x-landing.living.rental-card :rental="$rental" />
            @endforeach
            <x-landing.living.request-card :card="$request" />
        </div>
    </div>
</section>
