<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancellationPolicyRule extends Model
{
    protected $fillable = [
        'cancellation_policy_id',
        'days_before_min',
        'refund_percent',
        'fee_fixed',
        'sort_order',
    ];

    protected $casts = [
        'days_before_min' => 'integer',
        'refund_percent' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class, 'cancellation_policy_id');
    }
}
