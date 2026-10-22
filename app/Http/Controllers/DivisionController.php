<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelsSearchRequest;
use App\Models\ContentBlock;
use App\Models\ListingType;
use App\Models\Setting;
use App\Services\DivisionCatalog;
use App\Services\Hotel\HotelsStaySearchService;
use App\Services\MediaResolver;
use App\Support\BrandAssets;
use Carbon\Carbon;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function hospitality(HotelsSearchRequest $request, HotelsStaySearchService $search): View
    {
        $destination = $request->input('destination');
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $guests = (int) ($request->input('guests') ?: 2);

        $searching = $request->isSearchAttempt();
        $stays = collect();
        $searchError = null;

        if ($searching) {
            $result = $search->search(
                filled($destination) ? (string) $destination : null,
                filled($checkIn) ? (string) $checkIn : null,
                filled($checkOut) ? (string) $checkOut : null,
                max(1, $guests),
            );
            $stays = $result['stays'];
            $searchError = $result['error'];

            if (auth()->check() && $stays->isNotEmpty()) {
                $ids = $stays->pluck('listing_id')->filter()->all();
                $favIds = auth()->user()->favorites()
                    ->whereIn('listings.id', $ids)
                    ->pluck('listings.id');
                $stays = $stays->map(function (array $stay) use ($favIds) {
                    $stay['is_favorited'] = $favIds->contains($stay['listing_id']);

                    return $stay;
                });
            }
        }

        $summary = null;
        $sort = $request->input('sort');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 12;
        $totalCount = $stays->count();
        $hasMore = false;

        if ($searching && $stays->isNotEmpty()) {
            if ($sort === 'price_asc') {
                // Bookable stays by nightly; enquire-only cards last.
                $stays = $stays->sortBy(function (array $stay) {
                    if (! empty($stay['enquire_only'])) {
                        return PHP_FLOAT_MAX;
                    }

                    return (float) ($stay['nightly'] ?? PHP_FLOAT_MAX);
                })->values();
            }
            // Cumulative "Show more": page 1 = 12, page 2 = 24, …
            $hasMore = $totalCount > ($page * $perPage);
            $stays = $stays->take($page * $perPage)->values();
        }

        if ($searching) {
            $summary = $this->hotelsSearchSummary(
                filled($destination) ? (string) $destination : null,
                filled($checkIn) ? (string) $checkIn : null,
                filled($checkOut) ? (string) $checkOut : null,
                max(1, $guests),
            );
        }

        return view('divisions.hotels', [
            'hotelsSearch' => [
                'active' => $searching,
                'destination' => $destination,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $guests,
                'stays' => $stays,
                'count' => $totalCount,
                'page' => $page,
                'per_page' => $perPage,
                'has_more' => $hasMore,
                'sort' => $sort === 'price_asc' ? 'price_asc' : null,
                'summary' => $summary,
                'error' => $searchError,
            ],
        ]);
    }

    /**
     * @return array{label: string, destination_label: string}
     */
    private function hotelsSearchSummary(?string $destination, ?string $checkIn, ?string $checkOut, int $guests): array
    {
        $destinationLabel = filled($destination) ? $destination : 'Showing all destinations';
        $parts = [$destinationLabel];

        if (filled($checkIn) && filled($checkOut)) {
            $tz = config('app.timezone');
            $in = Carbon::parse($checkIn, $tz)->timezone($tz);
            $out = Carbon::parse($checkOut, $tz)->timezone($tz);
            $parts[] = $in->format('j M').' to '.$out->format('j M');
        }

        $parts[] = $guests.' '.($guests === 1 ? 'guest' : 'guests');

        return [
            'label' => implode(' · ', $parts).' · Edit',
            'destination_label' => $destinationLabel,
        ];
    }

    public function living(): View
    {
        return view('divisions.living');
    }

    private function show(string $key, DivisionCatalog $catalog, MediaResolver $media): View
    {
        $division = $catalog->config($key);
        $prefix = $division['content_prefix'];
        $mediaPrefix = $division['media_prefix'];

        $heroTitle = ContentBlock::resolve("{$prefix}.hero.title")['title']
            ?? ContentBlock::resolve("{$prefix}.hero.title")['body'];
        $heroLead = ContentBlock::resolve("{$prefix}.hero.lead")['body'];
        $introTitle = ContentBlock::resolve("{$prefix}.intro.title")['title'];
        $introBody = ContentBlock::resolve("{$prefix}.intro.body")['body'];

        $steps = [];
        for ($i = 1; $i <= 3; $i++) {
            $block = ContentBlock::resolve("{$prefix}.steps.{$i}.title");
            if ($block['title'] || $block['body']) {
                $steps[] = ['title' => $block['title'], 'body' => $block['body']];
            }
        }

        $audience = null;
        if (! empty($division['sections']['audience'])) {
            $audience = [
                'tenants' => ContentBlock::resolve('living.tenants.title'),
                'landlords' => ContentBlock::resolve('living.landlords.title'),
            ];
        }

        $heroSlides = $media->collection("{$mediaPrefix}.hero");
        if ($heroSlides->isEmpty()) {
            $derived = $media->listingCoverFallback(
                $division['scope']['purpose'] ?? null,
                $division['scope']['type_slugs'] ?? null
            );
            $heroSlides = collect([$derived ?: $media->brandedFallback()]);
        }

        $introMedia = $media->resolve("{$mediaPrefix}.intro", 0, function () use ($media, $division) {
            return $media->listingCoverFallback(
                $division['scope']['purpose'] ?? null,
                $division['scope']['type_slugs'] ?? null
            );
        });

        $video = $media->forSlot("{$mediaPrefix}.video");
        $featured = $catalog->featured($key, 8);
        $stats = $catalog->stats($key);
        $destinations = $catalog->destinations($key);
        $amenities = $catalog->amenities($key);
        $faqs = $catalog->faqs($key);
        $reviews = $catalog->reviews($key);
        $browseHref = $catalog->browseHref($division);

        $searchTypes = ListingType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name', 'slug'])
            ->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])
            ->all();

        $cross = config('divisions.'.$division['cross_link']);
        $divisionName = Setting::getValue(
            $key === 'hotels' ? 'site.division.hotels' : 'site.division.living',
            $division['nav_label']
        );

        $og = $heroSlides->first();
        $ogImage = (! empty($og['src'])) ? $og['src'] : url(BrandAssets::ogImage());

        return view('divisions.show', [
            'divisionKey' => $key,
            'division' => $division,
            'divisionName' => $divisionName,
            'heroTitle' => $heroTitle,
            'heroLead' => $heroLead,
            'introTitle' => $introTitle,
            'introBody' => $introBody,
            'steps' => $steps,
            'audience' => $audience,
            'heroSlides' => $heroSlides,
            'introMedia' => $introMedia,
            'video' => $video,
            'featured' => $featured,
            'stats' => $stats,
            'destinations' => $destinations,
            'amenities' => $amenities,
            'faqs' => $faqs,
            'reviews' => $reviews,
            'browseHref' => $browseHref,
            'searchTypes' => $searchTypes,
            'cross' => $cross,
            'ogImage' => $ogImage,
            'currency' => session('currency', 'PKR'),
            'locationSuggestions' => $destinations->map(fn ($d) => $d['city']->displayLabel())->filter()->values()->all(),
        ]);
    }
}
