<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use App\Support\CountryContext;
use App\Support\Scopes\CountryScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use CountryScoped;
    use LogsAdminActivity;
    use SoftDeletes;

    public const STATUSES = ['pending', 'awaiting_approval', 'paid', 'failed', 'refund_pending', 'refunded'];

    public const GATEWAYS = ['bank_transfer', 'jazzcash', 'easypaisa'];

    public static function enabledGateways(?int $countryId = null): array
    {
        $countryId ??= CountryContext::current()?->id;
        if ($countryId) {
            $fromCountry = CountryPaymentMethod::query()
                ->where('country_id', $countryId)
                ->where('is_enabled', true)
                ->pluck('gateway')
                ->all();
            if ($fromCountry !== []) {
                return array_values($fromCountry);
            }
        }

        $gateways = ['bank_transfer'];
        if (config('payments.jazzcash.enabled')) {
            $gateways[] = 'jazzcash';
        }
        if (config('payments.easypaisa.enabled')) {
            $gateways[] = 'easypaisa';
        }

        return $gateways;
    }

    protected $fillable = [
        'uuid',
        'payable_type',
        'payable_id',
        'user_id',
        'country_id',
        'amount',
        'currency',
        'gateway',
        'status',
        'external_id',
        'idempotency_key',
        'proof_path',
        'meta',
        'paid_at',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (blank($payment->uuid)) {
                $payment->uuid = (string) Str::uuid();
            }
        });
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
