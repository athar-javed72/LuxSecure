@extends('include.master')

@section('header_mode', 'overlay')
@section('title', 'Elevating Spaces. Enriching Stays.')
@section('meta_description')
{{ ($siteName ?? config('app.name')).' — Hotels, residences and thoughtfully selected properties, presented with care.' }}
@endsection

@php
    $img = config('site-images');
    $slides = $img['hero_slides'] ?? [];
    $featuredSale = $houses->where('purpose', '!=', 'short_stay')->take(8);
    $featuredStays = $houses->where('purpose', 'short_stay')->take(8);
    if (app()->isProduction()) {
        $featuredStays = $featuredStays->reject(fn ($l) => str_contains((string) $l->description, 'sample') || str_contains((string) $l->description, 'Seeded'));
    }
    $typeIcons = config('search.type_icons');
    $searchTypes = \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->get(['name', 'slug'])->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->all();
    $currency = session('currency', optional($activeCountry ?? null)->currency_code ?? 'PKR');
@endphp

@push('styles')
<link rel="preload" as="image" href="{{ $slides[0]['desktop'] ?? $img['hero']['desktop'] }}" fetchpriority="high">
@endpush

@section('home_content')

{{-- Hero — media clips; search lives as next sibling so ivory cannot paint over it (A1) --}}
<section
    class="relative overflow-visible w-full min-h-[600px] h-[min(92svh,820px)] flex flex-col justify-end -mt-[var(--header-h)] pt-[var(--header-h)] z-0"
    data-hero-slideshow
    aria-roledescription="carousel"
    aria-label="Featured spaces"
    tabindex="0"
>
    <div class="fs-hero-media" aria-hidden="true">
        @foreach($slides as $i => $slide)
            <div
                class="fs-hero-slide {{ $i === 0 ? 'is-active' : '' }}"
                role="group"
                aria-label="{{ $i + 1 }} of {{ count($slides) }}"
                @if($i > 0) aria-hidden="true" @endif
            >
                <picture>
                    <source
                        media="(max-width: 767px)"
                        @if($i === 0) srcset="{{ $slide['mobile'] }}" @else data-srcset="{{ $slide['mobile'] }}" @endif
                    >
                    <img
                        @if($i === 0)
                            src="{{ $slide['desktop'] }}"
                            fetchpriority="high"
                        @else
                            data-src="{{ $slide['desktop'] }}"
                            src=""
                            loading="lazy"
                        @endif
                        alt="{{ $slide['alt'] }}"
                        width="2400"
                        height="1350"
                        decoding="async"
                        class="absolute inset-0 w-full h-full object-cover"
                        style="object-position: {{ $slide['object_position'] }}; transform-origin: {{ $slide['origin'] ?? 'center' }};"
                    >
                </picture>
            </div>
        @endforeach
        <div class="absolute inset-0 fs-hero-overlay z-[2]"></div>
    </div>

    <div class="fs-hero-load relative z-10 w-full flex-1 flex flex-col justify-center pb-28 md:pb-36">
        <x-container class="w-full pt-8">
            <div class="max-w-[640px]">
                <div data-hero-step="1">
                    <x-eyebrow on-dark>Hospitality • Property • Living</x-eyebrow>
                </div>
                <h1 class="mt-4 text-[var(--color-ivory)]" data-hero-step="2">
                    Elevating Spaces.<br>Enriching Stays.
                </h1>
                <p class="mt-4 text-base md:text-[17px] text-[var(--color-sage)] fs-lead !text-[var(--color-sage)]" data-hero-step="3">
                    Hotels, residences and thoughtfully selected properties, presented with care.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3" data-hero-step="4">
                    <x-button variant="primary" href="{{ route('properties') }}" surface="dark" class="fs-btn-motion">Explore Properties</x-button>
                    <x-button variant="secondary" href="{{ route('contact') }}" surface="dark" class="fs-btn-motion">Start a Conversation</x-button>
                </div>
            </div>
        </x-container>
    </div>

    <div class="absolute bottom-8 right-[var(--gutter)] z-20 flex items-center gap-2" data-hero-controls>
        <button type="button" data-hero-prev class="fs-carousel__btn !bg-[rgba(13,29,25,.45)] !text-[var(--color-ivory)] !border-white/20" aria-label="Previous slide">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button type="button" data-hero-pause class="h-11 px-3 rounded-full text-xs font-semibold bg-[rgba(13,29,25,.45)] text-[var(--color-ivory)] border border-white/20" aria-pressed="false">Pause</button>
        <button type="button" data-hero-next class="fs-carousel__btn !bg-[rgba(13,29,25,.45)] !text-[var(--color-ivory)] !border-white/20" aria-label="Next slide">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div class="flex items-center gap-1.5 ml-1" role="tablist" aria-label="Slides">
            @foreach($slides as $i => $slide)
                <button type="button" data-hero-dot class="relative w-6 h-6 inline-flex items-center justify-center" aria-label="Go to slide {{ $i + 1 }}" @if($i === 0) aria-current="true" @endif>
                    <span class="block w-full h-0.5 bg-white/35 overflow-hidden rounded-full">
                        @if($i === 0)<span data-hero-progress class="block h-full w-0 bg-[var(--color-gold)]"></span>@endif
                    </span>
                </button>
            @endforeach
        </div>
        <span class="sr-only" data-hero-live aria-live="polite"></span>
    </div>
</section>

{{-- Search straddles hero → intro; must be a sibling above z-0 sections so bottom radius/shadow stay visible --}}
<div class="relative z-30 w-full -mt-[72px]" data-hero-search>
    <x-container class="w-full" data-hero-step="5">
        <x-property-search :suggestions="$locationSuggestions ?? []" :types="$searchTypes" :currency="$currency" />
    </x-container>
</div>

{{-- Brand intro --}}
<section class="relative z-0 fs-section-ivory pt-[calc(var(--section-y)+72px)] pb-[var(--section-y)]" aria-labelledby="intro-heading">
    <x-container class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-start" data-reveal>
        <div>
            <x-eyebrow>Maskanoor Group</x-eyebrow>
            <h2 id="intro-heading" class="mt-4">Spaces shape how we live, travel, and connect.</h2>
        </div>
        <div>
            <p class="fs-lead">
                We bring hospitality and property together through a thoughtful, detail-led approach.
            </p>
            <a href="{{ route('home') }}#about" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">
                About us →
            </a>
        </div>
    </x-container>
</section>

{{-- Divisions --}}
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="divisions-heading">
    <x-container>
        <x-section-heading
            eyebrow="Our divisions"
            title="Three ways to experience Maskanoor"
            lead="Hospitality, property and living — each with its own character."
            heading-id="divisions-heading"
        />
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6" data-reveal data-reveal-stagger>
            @foreach([
                ['key' => 'hotels', 'name' => 'Hotels', 'href' => route('divisions.hospitality'), 'copy' => 'Considered guest experiences, built around comfort, character and thoughtful service.', 'cta' => 'Discover Hotels'],
                ['key' => 'properties', 'name' => 'Properties', 'href' => route('properties'), 'copy' => 'A considered approach to property opportunities, ownership, leasing and management.', 'cta' => 'Discover Properties'],
                ['key' => 'living', 'name' => 'Living', 'href' => route('divisions.living'), 'copy' => 'Refined residences for stays that feel effortless, short-term or extended.', 'cta' => 'Discover Living'],
            ] as $div)
                <article class="group relative rounded-[var(--radius-card)] overflow-hidden">
                    <a href="{{ $div['href'] }}" class="absolute inset-0 z-10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)] rounded-[var(--radius-card)]" aria-label="{{ $div['cta'] }}"><span class="sr-only">{{ $div['cta'] }}</span></a>
                    <div class="relative aspect-[16/10] lg:aspect-[4/5] fs-card-media">
                        <img src="{{ $img['divisions'][$div['key']]['src'] }}" alt="{{ $img['divisions'][$div['key']]['alt'] }}" width="1200" height="1500" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 fs-division-overlay" aria-hidden="true"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 md:p-6 text-[var(--color-ivory)]">
                            <h3 class="font-[family-name:var(--font-display)] text-[30px] md:text-[32px] font-semibold text-[var(--color-ivory)]">{{ $div['name'] }}</h3>
                            <p class="mt-2 text-[15px] text-[var(--color-ivory)]/90 max-w-[36ch]">{{ $div['copy'] }}</p>
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[var(--color-gold)] transition-transform duration-200 group-hover:translate-x-1">{{ $div['cta'] }} →</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </x-container>
</section>

{{-- Browse types --}}
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="types-heading">
    <x-container>
        <x-section-heading
            eyebrow="Explore"
            title="Browse by property type"
            lead="Start with what you are looking for."
            heading-id="types-heading"
        />
        <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-4" data-reveal data-reveal-stagger>
            @php $homeTypes = \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->take(4)->get(); @endphp
            @foreach($homeTypes as $homeType)
                @php
                    $href = $homeType->slug === 'hotel' ? route('divisions.hospitality') : route('properties', ['type' => $homeType->slug]);
                    $count = \App\Models\Listing::query()->published()->where('listing_type_id', $homeType->id)->count();
                @endphp
                <a href="{{ $href }}" class="flex items-center gap-3 h-24 px-4 rounded-[var(--radius-card)] bg-white border border-[var(--color-border)] hover:border-[color-mix(in_srgb,var(--color-gold-dark)_40%,transparent)] transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]">
                    <span class="inline-flex text-[var(--color-forest)]" aria-hidden="true">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $typeIcons[$homeType->slug] ?? $typeIcons['house'] }}"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-[var(--color-ink)]">{{ $homeType->name }}</span>
                        @if($count > 0)
                            <span class="block text-xs text-[var(--color-muted)] mt-0.5 fs-numeral">{{ $count }} {{ \Illuminate\Support\Str::plural('listing', $count) }}</span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </x-container>
</section>

{{-- Featured properties carousel --}}
@if($featuredSale->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="featured-heading" data-carousel>
    <x-container>
        <x-section-heading
            eyebrow="Curated"
            title="Featured properties"
            lead="A selection of curated listings ready to explore."
            heading-id="featured-heading"
            action-href="{{ route('properties') }}"
            action-label="View all"
        >
            <x-slot:controls>
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous properties" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next properties">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </x-slot:controls>
        </x-section-heading>
        {{-- TODO owner: confirm verification claim — using curated until a real process exists. --}}
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0" aria-label="Featured properties">
                @foreach($featuredSale as $house)
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <x-property-card :property="$house" :is-sample="true" />
                    </div>
                @endforeach
            </div>
            <div class="fs-carousel__progress" aria-hidden="true"><span data-carousel-thumb></span></div>
        </div>
    </x-container>
</section>
@endif

{{-- Featured stays --}}
@if($featuredStays->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="stays-heading" data-carousel>
    <x-container>
        <x-section-heading
            eyebrow="Hospitality"
            title="Featured stays"
            lead="Short stays with the same care as a private residence."
            heading-id="stays-heading"
            action-href="{{ route('properties', ['purpose' => 'short_stay']) }}"
            action-label="View all"
        >
            <x-slot:controls>
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous stays" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next stays">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </x-slot:controls>
        </x-section-heading>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0" aria-label="Featured stays">
                @foreach($featuredStays as $stay)
                    <div class="fs-carousel__slide" data-carousel-slide>
                        {{-- Demo/seed stays always marked SAMPLE (A10 / A18) --}}
                        <x-property-card :property="$stay" :is-sample="true" />
                    </div>
                @endforeach
            </div>
            <div class="fs-carousel__progress" aria-hidden="true"><span data-carousel-thumb></span></div>
        </div>
    </x-container>
</section>
@endif

{{-- About --}}
<section id="about" class="relative z-0 fs-section fs-section-ivory-50 scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="about-heading">
    <x-container>
        <x-split-section>
            <x-slot:media>
                <div class="aspect-[4/5] w-full rounded-[var(--radius-card)] overflow-hidden" data-reveal>
                    <img src="{{ $img['about']['src'] }}" alt="{{ $img['about']['alt'] }}" width="1200" height="1500" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>
            </x-slot:media>
            <div data-reveal>
                <x-section-heading
                    eyebrow="Why Maskanoor"
                    title="A quieter, more considered way to find a place."
                    lead="Principles that guide how we present spaces and welcome guests."
                    heading-id="about-heading"
                />
                <div class="mt-10 space-y-6">
                    @foreach([
                        ['01', 'Thoughtful by design', 'Every detail — from materials to messaging — is chosen with care.'],
                        ['02', 'Personal by nature', 'We favour clarity and conversation over volume and noise.'],
                        // TODO owner: confirm international footprint wording.
                        ['03', 'International in outlook', 'An international outlook, with respect for local context.'],
                    ] as [$num, $label, $copy])
                        <div class="pt-5 border-t border-[var(--color-border)]">
                            <p class="font-[family-name:var(--font-display)] text-xl text-[var(--color-gold-dark)] fs-numeral">{{ $num }}</p>
                            <h3 class="mt-2 text-[var(--color-ink)]">{{ $label }}</h3>
                            <p class="mt-2 text-sm text-[var(--color-muted-strong)] max-w-[48ch]">{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-split-section>
    </x-container>
</section>

{{-- How we help --}}
<section id="services" class="relative z-0 fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="services-heading">
    <x-container>
        <x-section-heading
            eyebrow="Support"
            title="How we help"
            lead="Guidance from first enquiry through to a confident next step."
            heading-id="services-heading"
        />
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6" data-reveal data-reveal-stagger>
            @foreach([
                ['title' => 'Buying guidance', 'copy' => 'Shortlist homes that match your budget, lifestyle, and timeline.', 'href' => route('contact', ['enquiry' => 'buying']), 'icon' => 'M3 10l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z'],
                ['title' => 'Rental support', 'copy' => 'Compare apartments and residences with clear pricing and next steps.', 'href' => route('contact', ['enquiry' => 'rental']), 'icon' => 'M4 21V3h16v18M8 7h2M8 11h2M14 7h2M14 11h2'],
                ['title' => 'Listing help', 'copy' => 'Present your property with care to serious buyers and renters.', 'href' => route('contact', ['enquiry' => 'listing']), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ] as $service)
                <div class="fs-card p-8 h-full flex flex-col">
                    <span class="inline-flex text-[var(--color-forest)]" aria-hidden="true">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $service['icon'] }}"/></svg>
                    </span>
                    <h3 class="mt-5">{{ $service['title'] }}</h3>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)] flex-1">{{ $service['copy'] }}</p>
                    <a href="{{ $service['href'] }}" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">Talk to us →</a>
                </div>
            @endforeach
        </div>
    </x-container>
</section>

{{-- Image break --}}
<section class="relative z-0 w-full overflow-hidden fs-break-media" data-break aria-label="Brand statement">
    <picture>
        <source media="(max-width: 767px)" srcset="{{ $img['break']['mobile'] }}">
        <img src="{{ $img['break']['desktop'] }}" alt="{{ $img['break']['alt'] }}" width="2400" height="1030" loading="lazy" decoding="async" class="w-full aspect-[4/5] md:aspect-[21/9] object-cover">
    </picture>
    <div class="absolute inset-0 fs-break-overlay" aria-hidden="true"></div>
    <div class="absolute inset-0 flex items-center justify-center px-6">
        <p class="fs-break-quote font-[family-name:var(--font-display)] font-medium text-[var(--color-ivory)] text-center text-[clamp(28px,4vw,52px)] leading-tight max-w-[18ch]">
            A quieter expression of luxury.
        </p>
    </div>
</section>

@if($agents->isNotEmpty())
<section id="agents" class="relative z-0 fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="agents-heading" data-carousel>
    <x-container>
        <x-section-heading eyebrow="People" title="Meet our agents" lead="Local experts ready to help." heading-id="agents-heading">
            <x-slot:controls>
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous agents" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg></button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next agents"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
                </div>
            </x-slot:controls>
        </x-section-heading>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0" aria-label="Agents">
                @foreach ($agents->take(8) as $agent)
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <x-agent-card :name="$agent->user->name" :role="$agent->display_role" :image="$agent->photo_url" :listings="$agent->user->agent_listings_count ?? null" :phone="$agent->phone" :verified="$agent->is_verified || $agent->user?->isKycVerified()" :rating="$agent->averageRating()" href="#agents" />
                    </div>
                @endforeach
            </div>
            <div class="fs-carousel__progress"><span data-carousel-thumb></span></div>
        </div>
    </x-container>
</section>
@endif

@if($testimonials->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="testimonials-heading" data-carousel>
    <x-container>
        <x-section-heading eyebrow="Voices" title="What clients say" lead="Words from people who worked with us." heading-id="testimonials-heading">
            <x-slot:controls>
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg></button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
                </div>
            </x-slot:controls>
        </x-section-heading>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0">
                @foreach ($testimonials as $item)
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <x-testimonial-card :quote="$item->quote" :name="$item->name" :role="$item->role" />
                    </div>
                @endforeach
            </div>
            <div class="fs-carousel__progress"><span data-carousel-thumb></span></div>
        </div>
    </x-container>
</section>
@endif

@if($posts->isNotEmpty())
<section id="blog" class="relative z-0 fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="blog-heading" data-carousel>
    <x-container>
        <x-section-heading eyebrow="Journal" title="From the blog" lead="Notes on places, stays and considered living." heading-id="blog-heading" action-href="{{ route('blog.index') }}">
            <x-slot:controls>
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous posts" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg></button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next posts"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
                </div>
            </x-slot:controls>
        </x-section-heading>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0">
                @foreach ($posts as $post)
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <x-blog-card :title="$post->title" :excerpt="$post->excerpt" :image="$post->featured_image" :date="optional($post->published_at)->toDateString()" :category="$post->category" :href="route('blog.show', $post->slug)" />
                    </div>
                @endforeach
            </div>
            <div class="fs-carousel__progress"><span data-carousel-thumb></span></div>
        </div>
    </x-container>
</section>
@endif

{{-- FAQ --}}
<section id="faq" class="relative z-0 fs-section fs-section-ivory-50 scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="faq-heading">
    <x-container class="grid lg:grid-cols-12 gap-10 lg:gap-16">
        <div class="lg:col-span-4" data-reveal>
            <x-section-heading
                eyebrow="Help"
                title="Frequently asked questions"
                lead="Quick answers before you reach out."
                heading-id="faq-heading"
            />
            {{-- TODO owner: confirm FAQ answers. --}}
        </div>
        <div class="lg:col-span-8 space-y-3" data-reveal>
            @foreach ($faqs as $index => $faq)
                <x-faq-item :question="$faq->question" :answer="$faq->answer" :open="$index === 0" />
            @endforeach
        </div>
    </x-container>
</section>

<x-cta-band
    class="pb-24 pt-8"
    :rows="[
        ['title' => 'Stay', 'copy' => 'Enquire about a short stay or hospitality partnership.', 'href' => route('contact', ['type' => 'living'])],
        ['title' => 'Property', 'copy' => 'Buying, renting, or listing a considered home.', 'href' => route('contact', ['type' => 'property'])],
        ['title' => 'Partnership', 'copy' => 'Explore working together across hospitality and property.', 'href' => route('contact', ['type' => 'partnership'])],
    ]"
    secondary-label="Browse properties"
    :secondary-href="route('properties')"
/>
@endsection
