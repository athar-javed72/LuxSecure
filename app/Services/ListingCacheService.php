<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListingCacheService
{
    public function rememberIndex(Request $request, callable $builder): LengthAwarePaginator
    {
        $key = $this->key('index', $request->query());
        $ttl = (int) config('luxsecure.listing_cache_ttl', 60);

        return Cache::store($this->store())->remember($key, $ttl, $builder);
    }

    public function rememberShow(int|string $idOrSlug, callable $builder): Listing
    {
        $key = $this->key('show', ['id' => (string) $idOrSlug]);
        $ttl = (int) config('luxsecure.listing_cache_ttl', 60);

        return Cache::store($this->store())->remember($key, $ttl, $builder);
    }

    public function flush(): void
    {
        // Database/file stores do not support tags; bump a version key instead.
        Cache::store($this->store())->forever('listings.cache.version', (string) now()->timestamp);
    }

    private function store(): string
    {
        $preferred = config('luxsecure.listing_cache_store');

        if ($preferred && config("cache.stores.{$preferred}")) {
            return $preferred;
        }

        return config('cache.default', 'file');
    }

    /** @param  array<string, mixed>  $parts */
    private function key(string $segment, array $parts): string
    {
        $version = Cache::store($this->store())->get('listings.cache.version', '1');
        ksort($parts);

        return 'listings.v'.$version.'.'.$segment.'.'.md5(json_encode($parts));
    }
}
