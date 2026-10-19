<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use App\Support\CountryCatalog;
use App\Support\CountryReadiness;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use LogsAdminActivity;

    protected $fillable = [
        'iso2',
        'iso3',
        'source_id',
        'name',
        'currency_code',
        'default_locale',
        'locales',
        'timezone',
        'phone_code',
        'phone_regex',
        'area_units',
        'tax_name',
        'tax_percent',
        'tax_needs_review',
        'invoice_prefix',
        'invoice_sequence',
        'is_active',
        'wind_down',
        'sort_order',
    ];

    protected $casts = [
        'locales' => 'array',
        'area_units' => 'array',
        'tax_percent' => 'decimal:4',
        'tax_needs_review' => 'boolean',
        'is_active' => 'boolean',
        'wind_down' => 'boolean',
        'invoice_sequence' => 'integer',
        'sort_order' => 'integer',
        'source_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => CountryCatalog::flush());
        static::deleted(fn () => CountryCatalog::flush());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CountrySetting::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(CountryPaymentMethod::class);
    }

    public function kycDocumentTypes(): HasMany
    {
        return $this->hasMany(KycDocumentType::class);
    }

    public function readiness(): array
    {
        return CountryReadiness::check($this);
    }

    public function isReady(): bool
    {
        return $this->readiness()['ready'];
    }

    public function acceptsNewListings(): bool
    {
        return $this->is_active && ! $this->wind_down;
    }

    public function enabledGateways(): array
    {
        return $this->paymentMethods()
            ->where('is_enabled', true)
            ->pluck('gateway')
            ->all();
    }

    public function nextInvoiceNumber(): string
    {
        $this->increment('invoice_sequence');
        $seq = (int) $this->fresh()->invoice_sequence;

        return ($this->invoice_prefix ?: $this->iso2).'-'.now()->format('Ymd').'-'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
