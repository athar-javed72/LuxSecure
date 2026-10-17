<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_rtl',
        'needs_native_review',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_rtl' => 'boolean',
        'needs_native_review' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
