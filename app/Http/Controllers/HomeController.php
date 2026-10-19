<?php

namespace App\Http\Controllers;

use App\Models\AgentProfile;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Listing;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
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

        return view('home', compact(
            'houses',
            'agents',
            'testimonials',
            'posts',
            'faqs',
            'locationSuggestions'
        ));
    }
}