<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'iso2',
        'name',
        'currency_code',
        'default_locale',
        'timezone',
        'phone_code',
        'phone_regex',
        'area_units',
        'tax_name',
        'tax_percent',
        'invoice_prefix',
        'invoice_sequence',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'area_units' => 'array',
        'tax_percent' => 'decimal:4',
        'is_active' => 'boolean',
        'invoice_sequence' => 'integer',
        'sort_order' => 'integer',
    ];

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
