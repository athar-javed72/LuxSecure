<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenancy extends Model
{
    public const STATUSES = ['active', 'ended', 'cancelled'];

    protected $fillable = [
        'listing_id',
        'tenant_id',
        'landlord_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'security_deposit',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(RentPayment::class)->orderBy('due_date');
    }

    public function isOwnedBy(User $user): bool
    {
        return (int) $this->landlord_id === (int) $user->id
            || (int) $this->tenant_id === (int) $user->id
            || $user->ownsListing($this->listing);
    }
}
