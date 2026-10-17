<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class RentPayment extends Model
{
    public const STATUSES = ['pending', 'paid', 'overdue', 'waived'];

    protected $fillable = [
        'tenancy_id',
        'due_date',
        'paid_at',
        'amount',
        'method',
        'status',
        'receipt_no',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function gatewayPayments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function markPaid(?string $method = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'method' => $method ?? $this->method ?? 'cash',
            'receipt_no' => $this->receipt_no ?: 'RCP-'.Str::upper(Str::random(10)),
        ]);
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, ['pending', 'overdue'], true)
            && $this->due_date->isPast();
    }
}
