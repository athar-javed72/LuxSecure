<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'symbol',
        'decimals',
        'rate_to_base',
        'is_active',
    ];

    protected $casts = [
        'decimals' => 'integer',
        'rate_to_base' => 'decimal:8',
        'is_active' => 'boolean',
    ];
}
