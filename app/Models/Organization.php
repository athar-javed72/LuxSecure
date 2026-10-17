<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'owner_id',
        'country_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Organization $org) {
            if (blank($org->slug)) {
                $org->slug = Str::slug($org->name) ?: 'organization';
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
