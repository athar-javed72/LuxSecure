<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attributes()
    {
        return $this->hasMany(TypeAttribute::class)->orderBy('sort_order');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
