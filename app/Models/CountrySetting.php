<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class CountrySetting extends Model
{
    protected $fillable = [
        'country_id',
        'key',
        'value',
        'type',
        'label',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public static function getValue(int $countryId, string $key, mixed $default = null): mixed
    {
        $cacheKey = "country_settings.{$countryId}";
        $settings = Cache::remember($cacheKey, 300, function () use ($countryId) {
            return static::query()->where('country_id', $countryId)->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }

    public static function setValue(int $countryId, string $key, mixed $value, string $type = 'string', ?string $label = null): self
    {
        $row = static::query()->updateOrCreate(
            ['country_id' => $countryId, 'key' => $key],
            [
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                'type' => $type,
                'label' => $label,
            ]
        );
        Cache::forget("country_settings.{$countryId}");

        return $row;
    }
}
