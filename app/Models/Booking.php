<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public const ACTIVE_STATUSES = ['pending', 'confirmed'];

    public const CANCELLATION_POLICIES = ['flexible', 'moderate', 'strict'];

    protected $fillable = [
        'user_id',
        'listing_id',
        'room_type_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'total',
        'status',
        'cancellation_policy',
        'notes',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'guests' => 'integer',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function isOwnedByManager(User $user): bool
    {
        return $user->ownsListing($this->listing);
    }
}
