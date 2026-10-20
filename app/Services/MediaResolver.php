<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\MediaAsset;
use App\Support\BrandAssets;
use App\Support\StorageDisk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MediaResolver
{
    public function forSlot(string $slot, ?int $index = null): ?array
    {
        $items = $this->collection($slot);
        if ($items->isEmpty()) {
            return null;
        }
        if ($index === null) {
            return $items->first();
        }

        return $items->get($index);
    }

    public function collection(string $slot): Collection
    {
        $cacheKey = 'media.slot.'.$slot.(app()->isProduction() ? '.prod' : '.all');

        // Cache plain arrays only — never Eloquent Collections (avoids __PHP_Incomplete_Class).
        $cached = Cache::get($cacheKey);
        if (! is_array($cached)) {
            $cached = MediaAsset::query()
                ->visible()
                ->forSlot($slot)
                ->get()
                ->map(fn (MediaAsset $a) => $this->normalize($a))
                ->values()
                ->all();
            Cache::put($cacheKey, $cached, 600);
        }

        return collect($cached);
    }

    public function resolve(string $slot, ?int $index = null, ?callable $derived = null): array
    {
        $item = $this->forSlot($slot, $index);
        if ($item) {
            return $item;
        }

        if ($derived) {
            $fallback = $derived();
            if (is_array($fallback) && ! empty($fallback['src'])) {
                return $fallback;
            }
        }

        return $this->brandedFallback();
    }

    public function brandedFallback(): array
    {
        return [
            'kind' => 'branded',
            'type' => 'image',
            'src' => null,
            'poster' => null,
            'alt' => '',
            'decorative' => true,
            'caption' => null,
            'object_position' => '50% 50%',
            'variants' => [],
            'embed_url' => null,
            'width' => 2400,
            'height' => 1350,
            'monogram' => BrandAssets::logo('monogram-nostar', 'dark'),
        ];
    }

    public function listingCoverFallback(?string $purpose = null, ?array $typeSlugs = null): ?array
    {
        $q = Listing::query()->published()->with(['images', 'listingType'])->latest();
        if ($purpose) {
            $q->where('purpose', $purpose);
        }
        if ($typeSlugs) {
            $q->whereHas('listingType', fn ($t) => $t->whereIn('slug', $typeSlugs));
        }
        $listing = $q->first();
        if (! $listing) {
            return null;
        }
        $src = $listing->featured_image
            ?: optional($listing->images->first())->path;
        if (! $src) {
            return null;
        }

        return [
            'kind' => 'derived',
            'type' => 'image',
            'src' => StorageDisk::url($src),
            'poster' => null,
            'alt' => $listing->title,
            'decorative' => false,
            'caption' => null,
            'object_position' => '50% 50%',
            'variants' => [],
            'embed_url' => null,
            'width' => 1200,
            'height' => 1500,
        ];
    }

    public static function forgetSlot(string $slot): void
    {
        Cache::forget('media.slot.'.$slot.'.prod');
        Cache::forget('media.slot.'.$slot.'.all');
    }

    private function normalize(MediaAsset $asset): array
    {
        $variants = [];
        foreach ($asset->variantUrls() as $ext => $byWidth) {
            if (! is_array($byWidth)) {
                continue;
            }
            foreach ($byWidth as $w => $path) {
                $variants[$ext][(int) $w] = StorageDisk::url($path);
            }
        }

        return [
            'kind' => 'slot',
            'id' => $asset->id,
            'type' => $asset->type,
            'src' => $asset->type === 'embed' ? null : $asset->url(),
            'poster' => $asset->poster_path ? $asset->posterUrl() : null,
            'alt' => $asset->altText(),
            'decorative' => (bool) $asset->decorative,
            'caption' => $asset->caption,
            'object_position' => $asset->objectPosition(),
            'variants' => $variants,
            'embed_url' => $asset->type === 'embed' ? $asset->embedWatchUrl() : null,
            'embed_provider' => $asset->embed_provider,
            'width' => (int) data_get($asset->meta, 'width', 2400),
            'height' => (int) data_get($asset->meta, 'height', 1350),
            'is_demo' => (bool) $asset->is_demo,
        ];
    }
}
