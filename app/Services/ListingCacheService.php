<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ListingCacheService
{
    /**
     * Cache listing IDs + pagination meta, then hydrate models.
     *
     * @param  callable(): LengthAwarePaginator  $builder
     */
    public function rememberIndex(Request $request, callable $builder): LengthAwarePaginator
    {
        $key = $this->key('index', $request->query());
        $ttl = (int) config('luxsecure.listing_cache_ttl', 60);

        $payload = Cache::store($this->store())->remember($key, $ttl, function () use ($builder) {
            /** @var LengthAwarePaginator $paginator */
            $paginator = $builder();

            return [
                'ids' => $paginator->getCollection()->modelKeys(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ];
        });

        $order = array_flip($payload['ids']);
        $items = Listing::query()
            ->with(['images', 'listingType'])
            ->whereIn('id', $payload['ids'])
            ->get()
            ->sortBy(fn (Listing $listing) => $order[$listing->id] ?? 0)
            ->values();

        return new Paginator(
            $items,
            $payload['total'],
            $payload['per_page'],
            $payload['current_page'],
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    /**
     * @param  callable(): Listing  $builder
     */
    public function rememberShow(int|string $idOrSlug, callable $builder): Listing
    {
        $key = $this->key('show', ['id' => (string) $idOrSlug]);
        $ttl = (int) config('luxsecure.listing_cache_ttl', 60);

        $id = Cache::store($this->store())->remember($key, $ttl, function () use ($builder) {
            return $builder()->id;
        });

        return Listing::query()
            ->published()
            ->with(['images', 'listingType', 'city', 'area'])
            ->whereKey($id)
            ->firstOrFail();
    }

    public function flush(): void
    {
        Cache::store($this->store())->forever(
            'listings.cache.version',
            (string) Str::uuid().'-'.str_replace('.', '', (string) microtime(true))
        );
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
