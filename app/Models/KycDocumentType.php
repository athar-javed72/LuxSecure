<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycDocumentType extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'code',
        'is_required',
        'for_roles',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'for_roles' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
