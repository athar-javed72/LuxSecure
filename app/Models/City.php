<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'name', 'slug', 'is_active'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
