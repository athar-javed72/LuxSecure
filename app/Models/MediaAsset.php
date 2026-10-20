<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use App\Support\StorageDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    use LogsAdminActivity;

    public const TYPES = ['image', 'video', 'embed'];

    protected $fillable = [
        'slot',
        'type',
        'disk',
        'path',
        'poster_path',
        'embed_provider',
        'embed_id',
        'alt',
        'decorative',
        'caption',
        'focal_x',
        'focal_y',
        'sort',
        'is_active',
        'is_demo',
        'meta',
    ];

    protected $casts = [
        'decorative' => 'boolean',
        'is_active' => 'boolean',
        'is_demo' => 'boolean',
        'meta' => 'array',
        'focal_x' => 'integer',
        'focal_y' => 'integer',
        'sort' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSlot(Builder $query, string $slot): Builder
    {
        return $query->where('slot', $slot)->orderBy('sort')->orderBy('id');
    }

    public function scopeVisible(Builder $query): Builder
    {
        $query->active();
        if (app()->isProduction()) {
            $query->where('is_demo', false);
        }

        return $query;
    }

    public function url(): string
    {
        if ($this->type === 'embed') {
            return $this->embedWatchUrl();
        }

        return StorageDisk::url($this->path);
    }

    public function posterUrl(): string
    {
        return StorageDisk::url($this->poster_path);
    }

    public function objectPosition(): string
    {
        return ($this->focal_x ?? 50).'% '.($this->focal_y ?? 50).'%';
    }

    public function altText(): string
    {
        if ($this->decorative) {
            return '';
        }

        return (string) ($this->alt ?? '');
    }

    public function embedWatchUrl(): string
    {
        if ($this->embed_provider === 'youtube' && $this->embed_id) {
            return 'https://www.youtube-nocookie.com/embed/'.$this->embed_id;
        }
        if ($this->embed_provider === 'vimeo' && $this->embed_id) {
            return 'https://player.vimeo.com/video/'.$this->embed_id;
        }

        return '';
    }

    public function variantUrls(): array
    {
        $meta = $this->meta ?? [];

        return is_array($meta['variants'] ?? null) ? $meta['variants'] : [];
    }
}
