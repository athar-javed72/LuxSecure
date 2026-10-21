<?php

namespace App\Http\Controllers;

use App\Models\ContentBlock;
use App\Models\ListingType;
use App\Models\Setting;
use App\Services\DivisionCatalog;
use App\Services\MediaResolver;
use App\Support\BrandAssets;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function hospitality(): View
    {
        return view('divisions.hotels');
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
