<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, LogsAdminActivity, SoftDeletes;

    public const PURPOSES = ['sale', 'rent', 'short_stay'];

    public const PRICE_UNITS = ['total', 'per_month', 'per_night'];

    public const STATUSES = ['draft', 'pending', 'published', 'rejected', 'sold', 'rented', 'archived'];

    public const AREA_UNITS = ['sq_ft', 'marla', 'kanal', 'sq_m'];

    protected $fillable = [
        'listing_type_id',
        'title',
        'slug',
        'location',
        'purpose',
        'price',
        'price_unit',
        'currency',
        'status',
        'attributes',
        'city_id',
        'area_id',
        'area_size',
        'area_unit',
        'owner_id',
        'agent_id',
        'description',
        'featured_image',
        'floor_plan_path',
        'video_url',
        'private_document_path',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'area_size' => 'decimal:2',
        'attributes' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            if (blank($listing->slug)) {
                $listing->slug = static::uniqueSlug($listing->title);
            }
            if (blank($listing->price_unit)) {
                $listing->price_unit = match ($listing->purpose) {
                    'rent' => 'per_month',
                    'short_stay' => 'per_night',
                    default => 'total',
                };
            }
            if (blank($listing->currency)) {
                $listing->currency = 'PKR';
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'listing';
        $slug = $base;
        $i = 1;
        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /** @return array<string, mixed> */
    public function attributeValues(): array
    {
        return $this->getAttribute('attributes') ?? [];
    }

    public function attributeValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->attributeValues(), $key, $default);
    }

    public function listingType()
    {
        return $this->belongsTo(ListingType::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function inquiries()
    {
        return $this->hasMany(ContactInquiry::class);
    }

    public function tenancies()
    {
        return $this->hasMany(Tenancy::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->approved();
    }

    public function averageRating(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getBedroomsAttribute(): mixed
    {
        return $this->attributeValue('bedrooms');
    }

    public function getBathroomsAttribute(): mixed
    {
        return $this->attributeValue('bathrooms');
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $first = $this->relationLoaded('images') && $this->images->isNotEmpty()
            ? $this->images->first()
            : $this->images()->first();
        if ($first) {
            return \App\Support\StorageDisk::url($first->path);
        }
        if ($this->featured_image) {
            return \App\Support\StorageDisk::url($this->featured_image);
        }

        return '';
    }

    public function getFormattedPriceAttribute(): string
    {
        $amount = (float) $this->price;
        $suffix = match ($this->price_unit) {
            'per_month' => '/mo',
            'per_night' => '/night',
            default => '',
        };

        if ($this->purpose === 'sale' || $this->price_unit === 'total') {
            return $this->currency.' '.number_format($amount / 1_00_00_000, 1).' Cr'.$suffix;
        }

        return $this->currency.' '.number_format($amount).$suffix;
    }

    public function getTypeNameAttribute(): string
    {
        return $this->listingType?->name ?? '';
    }
}
