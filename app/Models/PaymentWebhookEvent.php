<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    protected $fillable = [
        'gateway',
        'event_id',
        'payload',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
