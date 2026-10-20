<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'state_name',
        'state_code',
        'source_id',
        'latitude',
        'longitude',
        'population',
        'cover_path',
        'cover_focal_x',
        'cover_focal_y',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'population' => 'integer',
        'source_id' => 'integer',
        'cover_focal_x' => 'integer',
        'cover_focal_y' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    public function displayLabel(): string
    {
        $parts = array_filter([$this->name, $this->state_name]);

        return implode(', ', $parts);
    }
}
