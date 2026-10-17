<?php

namespace App\Services\Hotel;

use App\Models\RoomType;
use App\Models\SeasonalPrice;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

class BookingPricingService
{
    /**
     * Calculate total stay price (exclusive of checkout night).
     *
     * @return array{nights: int, total: float, nightly: list<array{date: string, price: float}>}
     */
    public function calculate(RoomType $roomType, Carbon|string $checkIn, Carbon|string $checkOut): array
    {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();

        if ($checkOut->lte($checkIn)) {
            throw new InvalidArgumentException('Check-out must be after check-in.');
        }

        $seasonal = $roomType->relationLoaded('seasonalPrices')
            ? $roomType->seasonalPrices
            : $roomType->seasonalPrices()->get();

        $nightly = [];
        $total = 0.0;

        foreach (CarbonPeriod::create($checkIn, $checkOut->copy()->subDay()) as $night) {
            $price = (float) $roomType->base_price;

            /** @var SeasonalPrice|null $match */
            $match = $seasonal->first(function (SeasonalPrice $s) use ($night) {
                return $night->betweenIncluded($s->start_date, $s->end_date);
            });

            if ($match) {
                $price = (float) $match->price_per_night;
            }

            $nightly[] = ['date' => $night->toDateString(), 'price' => $price];
            $total += $price;
        }

        return [
            'nights' => count($nightly),
            'total' => round($total, 2),
            'nightly' => $nightly,
        ];
    }
}
