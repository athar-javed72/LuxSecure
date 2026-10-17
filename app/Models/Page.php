<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use LogsAdminActivity;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (blank($page->slug)) {
                $page->slug = Str::slug($page->title) ?: 'page';
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
