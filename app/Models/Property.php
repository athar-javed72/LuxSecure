<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'type',
        'price',
        'description',
        'bedrooms',
        'bathrooms',
        'featured_image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    public const TYPES = ['House', 'Villa', 'Apartment', 'Bungalow'];

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $first = $this->relationLoaded('images') && $this->images->isNotEmpty()
            ? $this->images->first()
            : $this->images()->first();
        if ($first) {
            return asset('storage/' . $first->path);
        }
        if ($this->featured_image) {
            if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
                return $this->featured_image;
            }
            return asset('storage/' . $this->featured_image);
        }
        return '';
    }
}
