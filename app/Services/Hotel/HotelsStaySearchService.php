<?php

namespace App\Services\Hotel;

use App\Models\Listing;
use App\Models\RoomType;
use App\Support\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class HotelsStaySearchService
{
    public function __construct(
        private readonly BookingAvailabilityService $availability,
        private readonly BookingPricingService $pricing,
    ) {}

    /**
     * Search published short-stay listings in Hotels context.
     *
     * @return array{stays: Collection<int, array<string, mixed>>, error: string|null}
     */
    public function search(?string $destination, ?string $checkIn, ?string $checkOut, int $guests = 1): array
    {
        try {
            $query = Listing::query()
                ->published()
                ->where('purpose', 'short_stay')
                // Load country; bookable stays still require an active country in mapStay.
                // No-inventory stays (Enquire cards) are included even when the country is inactive
                // so marketed destinations like Dubai / Kuala Lumpur still appear.
                ->with([
                    'images',
                    'city',
                    'country',
                    'roomTypes' => fn ($q) => $q->where('is_active', true)->with(['seasonalPrices', 'rooms']),
                ]);

            if (filled($destination)) {
                $term = $destination;
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('location', 'like', "%{$term}%")
                        ->orWhereHas('city', fn ($city) => $city->where('name', 'like', "%{$term}%"));
                });
            }

            $listings = $query->latest()->get();
            $hasDates = filled($checkIn) && filled($checkOut);

            $stays = $listings
                ->map(function (Listing $listing) use ($guests, $hasDates, $checkIn, $checkOut) {
                    return $this->mapStay($listing, $guests, $hasDates, $checkIn, $checkOut);
                })
                ->filter()
                ->values();

            return ['stays' => $stays, 'error' => null];
        } catch (Throwable $e) {
            report($e);

            return [
                'stays' => collect(),
                'error' => 'We could not check availability right now. Please try again.',
            ];
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mapStay(Listing $listing, int $guests, bool $hasDates, ?string $checkIn, ?string $checkOut): ?array
    {
        $activeRoomTypes = $listing->roomTypes;
        $hasBookableInventory = $activeRoomTypes->contains(
            fn (RoomType $rt) => $rt->rooms->contains(fn ($room) => $room->status === 'available')
        );
        $countryBookable = $listing->country
            && $listing->country->is_active
            && ! $listing->country->wind_down;

        // No active room types, or none with status=available rooms → enquire card.
        if ($activeRoomTypes->isEmpty() || ! $hasBookableInventory) {
            return $this->enquirePayload($listing);
        }

        // Inventory exists but country is inactive / wound down → omit (do not book).
        if (! $countryBookable) {
            return null;
        }

        $candidates = $activeRoomTypes
            ->filter(fn (RoomType $rt) => (int) $rt->capacity >= $guests)
            ->values();

        // Inventory exists but nothing fits this guest count → omit.
        if ($candidates->isEmpty()) {
            return null;
        }

        $currency = $listing->currency
            ?: $listing->country?->currency_code
            ?: 'PKR';

        if ($hasDates) {
            $available = $candidates
                ->filter(fn (RoomType $rt) => $this->availability->isAvailable($rt, $checkIn, $checkOut))
                ->values();

            // Real inventory, but fully booked for these dates → omit (not enquire).
            if ($available->isEmpty()) {
                return null;
            }

            $priced = $available
                ->map(function (RoomType $rt) use ($checkIn, $checkOut) {
                    $quote = $this->pricing->calculate($rt, $checkIn, $checkOut);

                    return [
                        'room_type' => $rt,
                        'quote' => $quote,
                        'nightly_avg' => $quote['nights'] > 0
                            ? round($quote['total'] / $quote['nights'], 2)
                            : (float) $rt->base_price,
                    ];
                })
                ->sortBy('quote.total')
                ->first();

            $nights = (int) $priced['quote']['nights'];
            $total = (float) $priced['quote']['total'];
            $nightly = (float) $priced['nightly_avg'];
            $showFrom = $available->count() > 1;

            return $this->stayPayload(
                $listing,
                $currency,
                $nightly,
                $total,
                $nights,
                $showFrom,
            );
        }

        $cheapest = $candidates->sortBy(fn (RoomType $rt) => (float) $rt->base_price)->first();
        $nightly = (float) $cheapest->base_price;
        $showFrom = $candidates->count() > 1;

        return $this->stayPayload(
            $listing,
            $currency,
            $nightly,
            null,
            null,
            $showFrom,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function enquirePayload(Listing $listing): array
    {
        $destination = $listing->city?->name
            ?: (string) ($listing->location ?? '');
        $image = $listing->primary_image_url ?: '/images/listings/hotel-1.jpg';

        return [
            'listing' => $listing,
            'listing_id' => $listing->id,
            'title' => $listing->title,
            'location' => $listing->location,
            'image' => $image,
            'image_alt' => $listing->title,
            'href' => route('contact', array_filter([
                'type' => 'hospitality',
                'stay' => $listing->title,
                'destination' => $destination !== '' ? $destination : null,
            ])),
            'enquire_only' => true,
            'currency' => null,
            'nightly' => null,
            'total' => null,
            'nights' => null,
            'show_from' => false,
            'price' => null,
            'price_suffix' => null,
            'total_label' => null,
            'cta_label' => 'Enquire for dates',
            'amenities' => ['Enquire for dates'],
            'is_favorited' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function stayPayload(
        Listing $listing,
        string $currency,
        float $nightly,
        ?float $total,
        ?int $nights,
        bool $showFrom,
    ): array {
        $image = $listing->primary_image_url ?: '/images/listings/hotel-1.jpg';

        return [
            'listing' => $listing,
            'listing_id' => $listing->id,
            'title' => $listing->title,
            'location' => $listing->location,
            'image' => $image,
            'image_alt' => $listing->title,
            'href' => route('properties.show', $listing),
            'enquire_only' => false,
            'currency' => $currency,
            'nightly' => $nightly,
            'total' => $total,
            'nights' => $nights,
            'show_from' => $showFrom,
            'price' => Money::formatCompact($nightly, $currency),
            'price_suffix' => '/night',
            'total_label' => $total !== null
                ? Money::formatCompact($total, $currency).($nights ? ' · '.$nights.' '.Str::plural('night', $nights) : '')
                : null,
            'amenities' => array_values(array_filter([
                $listing->roomTypes->count() > 1
                    ? $listing->roomTypes->count().' room types'
                    : null,
                'Free cancellation',
            ])),
            'is_favorited' => false,
        ];
    }
}
