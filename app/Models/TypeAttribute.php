<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAttribute extends Model
{
    use HasFactory;

    public const INPUT_TYPES = ['text', 'number', 'select', 'boolean'];

    protected $fillable = [
        'listing_type_id',
        'key',
        'label',
        'input_type',
        'options',
        'is_required',
        'is_filterable',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
    ];

    public function listingType()
    {
        return $this->belongsTo(ListingType::class);
    }
}
