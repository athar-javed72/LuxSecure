<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    protected $fillable = [
        'channel',
        'to',
        'body',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
