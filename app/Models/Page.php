<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use App\Support\CountryContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use LogsAdminActivity;

    public const TYPES = ['privacy', 'terms', 'refund', 'cookies', 'about', 'custom'];

    protected $fillable = [
        'title',
        'slug',
        'type',
        'country_id',
        'locale',
        'version',
        'effective_from',
        'status',
        'body',
        'is_published',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'effective_from' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (blank($page->slug)) {
                $page->slug = Str::slug($page->title) ?: 'page';
            }
            if (blank($page->locale)) {
                $page->locale = 'en';
            }
            if (blank($page->version)) {
                $page->version = '1.0';
            }
            if (blank($page->status)) {
                $page->status = $page->is_published ? 'published' : 'draft';
            }
        });

        static::saving(function (Page $page) {
            if ($page->status === 'published') {
                $page->is_published = true;
            }
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->where('is_published', true)->orWhere('status', 'published');
        });
    }

    public static function resolveLegal(string $type, ?int $countryId = null, ?string $locale = null): ?self
    {
        $countryId ??= CountryContext::current()?->id;
        $locale ??= session('locale', app()->getLocale() ?: 'en');

        $query = static::query()->published()->where('type', $type);

        $page = (clone $query)->where('country_id', $countryId)->where('locale', $locale)->latest('effective_from')->first();
        if ($page) {
            return $page;
        }

        $page = (clone $query)->where('country_id', $countryId)->where('locale', 'en')->latest('effective_from')->first();
        if ($page) {
            return $page;
        }

        $page = (clone $query)->whereNull('country_id')->where('locale', $locale)->latest('effective_from')->first();
        if ($page) {
            return $page;
        }

        return (clone $query)->whereNull('country_id')->latest('effective_from')->first();
    }
}
