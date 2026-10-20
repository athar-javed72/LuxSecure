<?php

namespace App\Http\Controllers;

use App\Models\AgentProfile;
use App\Models\BlogPost;
use App\Models\ContentBlock;
use App\Models\Faq;
use App\Models\Listing;
use App\Models\Testimonial;
use App\Services\DivisionCatalog;
use App\Services\MediaResolver;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(MediaResolver $media, DivisionCatalog $catalog): View
    {
        $houses = Listing::query()
            ->published()
            ->with(['images', 'listingType'])
            ->latest()
            ->take(12)
            ->get();

        $agents = AgentProfile::query()
            ->with(['user' => fn ($q) => $q->withCount('agentListings')])
            ->whereHas('user', fn ($u) => $u->role('agent')->where('is_active', true))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $testimonials = Testimonial::published()->take(6)->get();
        $posts = BlogPost::published()->latest('published_at')->take(3)->get();
        $faqs = Faq::published()->take(8)->get();

        $locationSuggestions = Listing::query()
            ->published()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->limit(40)
            ->pluck('location')
            ->filter()
            ->values()
            ->all();

        $heroSlides = $media->collection('home.hero');
        if ($heroSlides->isEmpty()) {
            $derived = $media->listingCoverFallback();
            $heroSlides = collect([$derived ?: $media->brandedFallback()]);
        }

        $introMedia = $media->forSlot('home.intro');
        $introCopy = ContentBlock::resolve('home.intro.title');
        $introBody = ContentBlock::resolve('home.intro.body');
        $homeStats = $introMedia ? [] : array_values(array_filter([
            ...$catalog->stats('hotels'),
            ...$catalog->stats('living'),
        ]));
        // Dedupe by label and require >=2 for strip
        $seen = [];
        $homeStats = collect($homeStats)->filter(function ($s) use (&$seen) {
            $label = $s['label'] ?? '';
            if (isset($seen[$label]) || ($s['value'] ?? 0) <= 0) {
                return false;
            }
            $seen[$label] = true;

            return true;
        })->take(3)->values()->all();

        $divisionCards = collect([
            ['key' => 'hotels', 'slot' => 'home.division.hotels', 'name' => 'Hotels', 'href' => route('divisions.hospitality'), 'copy' => 'Considered guest experiences, built around comfort, character and thoughtful service.', 'cta' => 'Discover Hotels', 'purpose' => 'short_stay'],
            ['key' => 'properties', 'slot' => 'home.division.properties', 'name' => 'Properties', 'href' => route('properties'), 'copy' => 'A considered approach to property opportunities, ownership, leasing and management.', 'cta' => 'Discover Properties', 'purpose' => 'sale'],
            ['key' => 'living', 'slot' => 'home.division.living', 'name' => 'Living', 'href' => route('divisions.living'), 'copy' => 'Refined residences for stays that feel effortless, short-term or extended.', 'cta' => 'Discover Living', 'purpose' => 'rent'],
        ])->map(function ($div) use ($media) {
            $div['media'] = $media->resolve($div['slot'], 0, fn () => $media->listingCoverFallback($div['purpose']));

            return $div;
        });

        $aboutMedia = $media->resolve('home.about', 0, fn () => $media->listingCoverFallback());
        $breakMedia = $media->forSlot('home.break');
        $homeVideo = $media->forSlot('home.video');

        $destinations = $catalog->destinations('hotels', 4)
            ->merge($catalog->destinations('living', 4))
            ->unique(fn ($d) => $d['city']->id)
            ->take(6)
            ->values();

        $featuredIds = $houses->pluck('id');
        $recent = Listing::query()
            ->published()
            ->with(['images', 'listingType'])
            ->whereNotIn('id', $featuredIds)
            ->latest()
            ->take(8)
            ->get();
        if ($recent->isEmpty()) {
            $recent = $houses->slice(4)->values();
        }

        return view('home', compact(
            'houses',
            'agents',
            'testimonials',
            'posts',
            'faqs',
            'locationSuggestions',
            'heroSlides',
            'introMedia',
            'introCopy',
            'introBody',
            'homeStats',
            'divisionCards',
            'aboutMedia',
            'breakMedia',
            'homeVideo',
            'destinations',
            'recent',
        ));
    }
}
