<?php

namespace App\Services\Rent;

use App\Models\RentPayment;
use App\Models\Tenancy;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RentScheduleGenerator
{
    /**
     * Generate monthly rent payment rows for a tenancy (does not persist).
     *
     * @return Collection<int, array{due_date: string, amount: string, status: string}>
     */
    public function generate(Tenancy $tenancy): Collection
    {
        $start = Carbon::parse($tenancy->start_date)->startOfDay();
        $end = $tenancy->end_date
            ? Carbon::parse($tenancy->end_date)->startOfDay()
            : $start->copy()->addYear()->subDay();

        if ($end->lt($start)) {
            return collect();
        }

        $rows = collect();
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $rows->push([
                'due_date' => $cursor->toDateString(),
                'amount' => (string) $tenancy->monthly_rent,
                'status' => 'pending',
            ]);
            $cursor->addMonthNoOverflow();
        }

        return $rows;
    }

    /**
     * Persist schedule payments for a tenancy (idempotent if none exist).
     *
     * @return Collection<int, RentPayment>
     */
    public function createFor(Tenancy $tenancy): Collection
    {
        if ($tenancy->payments()->exists()) {
            return $tenancy->payments;
        }

        return $this->generate($tenancy)->map(function (array $row) use ($tenancy) {
            return RentPayment::create([
                'tenancy_id' => $tenancy->id,
                'due_date' => $row['due_date'],
                'amount' => $row['amount'],
                'status' => $row['status'],
            ]);
        });
    }
}
