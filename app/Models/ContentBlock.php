<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ContentBlock extends Model
{
    use LogsAdminActivity;

    protected $fillable = [
        'key',
        'locale',
        'title',
        'body',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public static function resolve(string $key, ?string $locale = null): array
    {
        $locale ??= app()->getLocale() ?: 'en';
        $cacheKey = "content_block.{$locale}.{$key}";

        return Cache::remember($cacheKey, 600, function () use ($key, $locale) {
            $row = static::query()->where('key', $key)->where('locale', $locale)->first()
                ?? static::query()->where('key', $key)->where('locale', 'en')->first();

            $bundle = trans('content', [], $locale);
            if (! is_array($bundle)) {
                $bundle = trans('content', [], 'en');
            }
            $defaults = is_array($bundle) ? ($bundle[$key] ?? null) : null;
            if (! is_array($defaults)) {
                $defaults = ['title' => null, 'body' => null];
            }

            return [
                'title' => $row?->title ?: ($defaults['title'] ?? null),
                'body' => $row?->body ?: ($defaults['body'] ?? null),
                'meta' => $row?->meta ?? ($defaults['meta'] ?? []),
                'from_db' => (bool) $row,
            ];
        });
    }

    public static function forgetCache(string $key, string $locale = 'en'): void
    {
        Cache::forget("content_block.{$locale}.{$key}");
    }
}
