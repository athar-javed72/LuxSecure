<?php

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('booking_payments') || ! Schema::hasTable('payments')) {
            return;
        }

        $statusMap = [
            'pending' => 'pending',
            'paid' => 'paid',
            'refunded' => 'refunded',
            'failed' => 'failed',
        ];

        DB::table('booking_payments')->orderBy('id')->chunkById(100, function ($rows) use ($statusMap) {
            foreach ($rows as $row) {
                $idempotency = 'legacy-bp-'.$row->id;
                if (Payment::withTrashed()->where('idempotency_key', $idempotency)->exists()) {
                    continue;
                }

                $booking = DB::table('bookings')->where('id', $row->booking_id)->first();
                if (! $booking) {
                    continue;
                }

                $gateway = in_array((string) $row->method, Payment::GATEWAYS, true)
                    ? (string) $row->method
                    : 'bank_transfer';

                Payment::query()->create([
                    'uuid' => (string) Str::uuid(),
                    'payable_type' => Booking::class,
                    'payable_id' => $row->booking_id,
                    'user_id' => $booking->user_id,
                    'amount' => $row->amount,
                    'currency' => 'PKR',
                    'gateway' => $gateway,
                    'status' => $statusMap[$row->status] ?? 'pending',
                    'external_id' => $row->reference,
                    'idempotency_key' => $idempotency,
                    'paid_at' => $row->paid_at,
                    'meta' => [
                        'legacy_booking_payment_id' => $row->id,
                        'deprecated_source' => 'booking_payments',
                    ],
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Intentionally keep backfilled payments; they are the system of record.
    }
};
