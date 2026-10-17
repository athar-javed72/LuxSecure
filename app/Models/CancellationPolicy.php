<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CancellationPolicy extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'applies_to',
        'reference_date',
        'country_id',
        'organization_id',
        'is_default',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function purposeToAppliesTo(?string $purpose): string
    {
        return match ($purpose) {
            'rent' => 'rent',
            'sale' => 'sale',
            'short_stay' => 'hotel',
            default => 'hotel',
        };
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(CancellationPolicyRule::class)->orderByDesc('days_before_min');
    }

    public static function resolveForListing(Listing $listing): ?self
    {
        if ($listing->cancellation_policy_id) {
            $direct = static::query()->with('rules')->find($listing->cancellation_policy_id);
            if ($direct) {
                return $direct;
            }
        }

        $appliesTo = static::purposeToAppliesTo($listing->purpose);

        if ($listing->organization_id) {
            $org = static::query()
                ->with('rules')
                ->where('organization_id', $listing->organization_id)
                ->where('applies_to', $appliesTo)
                ->where('is_active', true)
                ->where('is_default', true)
                ->first();
            if ($org) {
                return $org;
            }
        }

        if ($listing->country_id) {
            $country = static::query()
                ->with('rules')
                ->where('country_id', $listing->country_id)
                ->whereNull('organization_id')
                ->where('applies_to', $appliesTo)
                ->where('is_active', true)
                ->where('is_default', true)
                ->first();
            if ($country) {
                return $country;
            }
        }

        $preferredSlug = match ($appliesTo) {
            'rent' => 'rent-standard',
            'sale' => 'sale-token',
            default => 'hotel-flexible',
        };

        $globals = static::query()
            ->with('rules')
            ->whereNull('country_id')
            ->whereNull('organization_id')
            ->where('applies_to', $appliesTo)
            ->where('is_active', true)
            ->where('is_default', true)
            ->get();

        if ($globals->isNotEmpty()) {
            return $globals->firstWhere('slug', $preferredSlug) ?? $globals->first();
        }

        return static::query()
            ->with('rules')
            ->whereNull('country_id')
            ->whereNull('organization_id')
            ->where('slug', $preferredSlug)
            ->first()
            ?? static::query()
                ->with('rules')
                ->whereNull('country_id')
                ->whereNull('organization_id')
                ->where('slug', 'flexible')
                ->first();
    }
}
