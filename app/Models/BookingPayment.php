<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @deprecated Legacy ledger. Do not write new rows — use App\Models\Payment.
 */
class BookingPayment extends Model
{
    public const STATUSES = ['pending', 'paid', 'refunded', 'failed'];

    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'status',
        'reference',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
