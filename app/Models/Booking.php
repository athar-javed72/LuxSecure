<?php

namespace App\Models;

use App\Support\Scopes\CountryScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use CountryScoped;
    use SoftDeletes;

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public const ACTIVE_STATUSES = ['pending', 'confirmed'];

    public const CANCELLATION_POLICIES = ['flexible', 'moderate', 'strict'];

    protected $fillable = [
        'country_id',
        'user_id',
        'listing_id',
        'room_type_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'total',
        'currency',
        'status',
        'cancellation_policy',
        'cancellation_policy_id',
        'notes',
        'hold_expires_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'guests' => 'integer',
        'total' => 'decimal:2',
        'hold_expires_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function isOwnedByManager(User $user): bool
    {
        if ($user->ownsListing($this->listing)) {
            return true;
        }

        // Fallback for unloaded listing edge cases — prefer ownsListing rules.
        if ($user->hasRole('organization_admin')
            && $user->organization_id
            && $this->listing?->organization_id
            && (int) $user->organization_id === (int) $this->listing->organization_id) {
            return true;
        }

        return $user->hasRole('hotel_manager')
            && (int) $this->listing?->manager_id === (int) $user->id;
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Canonical ledger — polymorphic payments. */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /** @deprecated Read-only legacy table; do not write. */
    public function legacyBookingPayments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function scopeBlockingInventory(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'confirmed')
                ->orWhere(function (Builder $pending) {
                    $pending->where('status', 'pending')
                        ->where(function (Builder $hold) {
                            $hold->whereNull('hold_expires_at')
                                ->orWhere('hold_expires_at', '>', now());
                        });
                });
        });
    }
}
