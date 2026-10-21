@props([
    'listings' => null,
])

@php
    $source = collect($listings ?? []);
    $picked = collect();
    foreach (['sale', 'rent', 'short_stay'] as $purpose) {
        $hit = $source->firstWhere('purpose', $purpose);
        if ($hit) {
            $picked->push($hit);
        }
    }
    foreach ($source as $listing) {
        if ($picked->count() >= 3) {
            break;
        }
        if (! $picked->contains(fn ($l) => $l->id === $listing->id)) {
            $picked->push($listing);
        }
    }

    $fallbackImages = [
        'sale' => '/images/listings/apartment-2.jpg',
        'rent' => '/images/listings/apartment-1.jpg',
        'short_stay' => '/images/listings/hotel-1.jpg',
    ];
@endphp

<section class="mg-section mg-featured" aria-labelledby="mg-featured-heading">
    <div class="mg-container">
        <div class="mg-featured__head">
            <div>
                <p class="mg-eyebrow">Curated</p>
                <h2 class="mg-h2" id="mg-featured-heading">Featured listings</h2>
            </div>
            <a href="{{ route('properties') }}" class="mg-featured__view">View all →</a>
        </div>

        <div class="mg-listings">
            @forelse($picked as $property)
                @php
                    $url = route('properties.show', $property);
                    $tag = match ($property->purpose) {
                        'rent' => 'For rent',
                        'short_stay' => 'Short stay',
                        default => 'For sale',
                    };
                    $priceAmount = \App\Support\Money::formatCompact($property->price, $property->currency);
                    $priceSuffix = '';
                    if ($property->purpose === 'short_stay' || $property->price_unit === 'per_night') {
                        $priceSuffix = ' /night';
                    } elseif ($property->price_unit === 'per_month' || $property->purpose === 'rent') {
                        $priceSuffix = ' /month';
                    } elseif (($property->purpose === 'sale' || $property->price_unit === 'total') && (float) $property->price >= 10_000_000) {
                        $priceAmount = \App\Support\Money::code($property->currency).' '.number_format((float) $property->price / 10_000_000, 1).' Cr';
                        $priceAmount = preg_replace('/\.0 Cr$/', ' Cr', $priceAmount);
                    }
                    $image = $property->primary_image_url
                        ?: ($fallbackImages[$property->purpose] ?? '/images/listings/apartment-1.jpg');
                    $specs = collect([
                        $property->bedrooms ? $property->bedrooms.' beds' : null,
                        $property->bathrooms ? $property->bathrooms.' baths' : null,
                    ])->filter()->implode(' · ');
                @endphp
                <article class="mg-listing-card">
                    <a href="{{ $url }}" class="mg-listing-card__link" aria-label="View {{ $property->title }}">
                        <div class="mg-listing-card__media">
                            <img src="{{ $image }}" alt="{{ $property->title }}" width="800" height="600" loading="lazy" decoding="async">
                            <span class="mg-listing-card__tag">{{ $tag }}</span>
                        </div>
                        <div class="mg-listing-card__body">
                            <p class="mg-listing-card__price">{{ $priceAmount }}{{ $priceSuffix }}</p>
                            <h3 class="mg-listing-card__title">{{ $property->title }}</h3>
                            <p class="mg-listing-card__area">{{ $property->location }}</p>
                            @if($specs !== '')
                                <p class="mg-listing-card__specs">{{ $specs }}</p>
                            @endif
                        </div>
                    </a>
                    <button
                        type="button"
                        class="mg-listing-card__save"
                        data-mg-save
                        aria-pressed="false"
                        aria-label="Save {{ $property->title }}"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 11c0 5.6-7 10-7 10z"/>
                        </svg>
                    </button>
                </article>
            @empty
                <p class="text-sm" style="color: var(--mg-muted);">Listings will appear here once they are published.</p>
            @endforelse
        </div>
    </div>
</section>
