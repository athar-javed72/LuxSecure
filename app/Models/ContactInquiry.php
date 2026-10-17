<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInquiry extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'message', 'listing_id', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function lead()
    {
        return $this->hasOne(Lead::class);
    }

    /** @deprecated Use listing() */
    public function property()
    {
        return $this->listing();
    }
}
