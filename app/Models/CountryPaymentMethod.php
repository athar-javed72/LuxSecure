<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryPaymentMethod extends Model
{
    protected $fillable = [
        'country_id',
        'gateway',
        'is_enabled',
        'meta',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'meta' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
