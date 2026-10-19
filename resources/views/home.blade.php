@extends('include.master')

@section('title', 'Elevating Spaces. Enriching Stays.')
@section('meta_description')
{{ ($siteName ?? config('app.name')).' — Hotels, residences and thoughtfully selected properties, presented with care.' }}
@endsection

@push('styles')
<link rel="preload" as="image" href="{{ config('site-images.hero.desktop') }}" fetchpriority="high">
@endpush

@section('home_content')
@php
    $img = config('site-images');
    $featuredSale = $houses->where('purpose', '!=', 'short_stay')->take(4);
    $featuredStays = $houses->where('purpose', 'short_stay')->take(4);
    $typeIcons = [
        'apartment' => 'M4 21V3h16v18M8 7h2M8 11h2M8 15h2M14 7h2M14 11h2M14 15h2',
        'plot' => 'M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4',
        'hotel' => 'M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6',
        'house' => 'M3 10l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z',
        'commercial' => 'M4 21V7l8-4 8 4v14M9 21v-6h6v6',
    ];
@endphp

{{-- 5.1 Hero --}}
<section class="relative w-full min-h-[600px] h-[min(92svh,820px)] flex flex-col justify-end overflow-hidden -mt-[var(--header-h)] pt-[var(--header-h)]">
    <picture class="absolute inset-0">
        <source media="(max-width: 767px)" srcset="{{ $img['hero']['mobile'] }}">
        <img
            src="{{ $img['hero']['desktop'] }}"
            alt="{{ $img['hero']['alt'] }}"
            width="2400"
            height="1350"
            fetchpriority="high"
            decoding="async"
            class="absolute inset-0 w-full h-full object-cover"
            style="object-position: {{ $img['hero']['object_position'] }};"
        >
    </picture>
    <div class="absolute inset-0 fs-hero-overlay" aria-hidden="true"></div>

    <x-container class="relative z-10 w-full pb-24 md:pb-28 pt-8">
        <div class="max-w-[640px]">
            <p class="fs-eyebrow-on-dark">Hospitality · Property · Living</p>
            <h1 class="mt-4 text-[var(--color-ivory)]">
                Elevating Spaces. Enriching Stays.
            </h1>
            <p class="mt-4 text-base md:text-[17px] text-[var(--color-sage)] max-w-[56ch]">
                Hotels, residences and thoughtfully selected properties, presented with care.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <x-button variant="primary" href="{{ route('properties') }}" surface="dark">Explore Properties</x-button>
                <x-button variant="secondary" href="{{ route('contact') }}" surface="dark">Start a Conversation</x-button>
            </div>
        </div>
    </x-container>

    <x-container class="relative z-[5] w-full -mb-14">
        <form action="{{ route('properties') }}" method="GET" class="w-full bg-white rounded-[var(--radius-card)] border border-[var(--color-border)] shadow-[var(--shadow-card)] p-4 sm:p-5 mx-0 max-md:mx-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <x-input label="Location" name="search" :value="request('search')" placeholder="City or area" />
                <x-select
                    label="Property type"
                    name="type"
                    :options="\App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->pluck('name', 'slug')->all()"
                    :selected="request('type')"
                    placeholder="All types"
                />
                <x-select
                    label="Budget"
                    name="price"
                    :options="[
                        '0-30000000' => 'Below 3 Crore',
                        '30000001-60000000' => '3–6 Crore',
                        '60000001-100000000' => '6–10 Crore',
                        '100000001-1000000000' => '10 Crore+',
                    ]"
                    :selected="request('price')"
                    placeholder="Any budget"
                />
                <x-button type="submit" variant="primary" surface="light" class="w-full">Search</x-button>
            </div>
        </form>
    </x-container>
</section>

{{-- 5.2 Brand intro --}}
<section class="fs-section-ivory pt-[calc(var(--section-y)+56px)] pb-[var(--section-y)]" aria-labelledby="intro-heading">
    <x-container class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-start">
        <div>
            <p class="fs-eyebrow">Maskanoor Group</p>
            <h2 id="intro-heading" class="mt-4">Spaces shape how we live, travel, and connect.</h2>
        </div>
        <div>
            <p class="fs-lead text-[var(--color-muted)]">
                We bring hospitality and property together through a thoughtful, detail-led approach.
            </p>
            <a href="{{ route('home') }}#about" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">
                About us →
            </a>
        </div>
    </x-container>
</section>

{{-- 5.3 Divisions --}}
<section class="fs-section fs-section-ivory-50" aria-labelledby="divisions-heading">
    <x-container>
        <x-section-heading
            eyebrow="Our divisions"
            title="Three ways to experience Maskanoor"
            lead="Hospitality, property and living — each with its own character."
            heading-id="divisions-heading"
        />
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                [
                    'key' => 'hotels',
                    'name' => 'Hotels',
                    'href' => route('divisions.hospitality'),
                    'copy' => 'Considered guest experiences, built around comfort, character and thoughtful service.',
                    'cta' => 'Discover Hotels',
                ],
                [
                    'key' => 'properties',
                    'name' => 'Properties',
                    'href' => route('properties'),
                    'copy' => 'A considered approach to property opportunities, ownership, leasing and management.',
                    'cta' => 'Discover Properties',
                ],
                [
                    'key' => 'living',
                    'name' => 'Living',
                    'href' => route('divisions.living'),
                    'copy' => 'Refined residences for stays that feel effortless, short-term or extended.',
                    'cta' => 'Discover Living',
                ],
            ] as $div)
                <article class="group relative rounded-[var(--radius-card)] overflow-hidden">
                    <a href="{{ $div['href'] }}" class="absolute inset-0 z-10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)] rounded-[var(--radius-card)]" aria-label="{{ $div['cta'] }}">
                        <span class="sr-only">{{ $div['cta'] }}</span>
                    </a>
                    <div class="relative aspect-[16/10] lg:aspect-[4/5]">
                        <img
                            src="{{ $img['divisions'][$div['key']]['src'] }}"
                            alt="{{ $img['divisions'][$div['key']]['alt'] }}"
                            width="1200"
                            height="1500"
                            loading="lazy"
                            decoding="async"
                            class="absolute inset-0 w-full h-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[rgba(13,29,25,.88)] via-[rgba(13,29,25,.35)] to-transparent" aria-hidden="true"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 md:p-6 text-[var(--color-ivory)]">
                            <h3 class="font-[family-name:var(--font-display)] text-[28px] font-semibold text-[var(--color-ivory)]">{{ $div['name'] }}</h3>
                            <p class="mt-2 text-sm text-[var(--color-sage)] max-w-[36ch]">{{ $div['copy'] }}</p>
                            <span class="mt-4 inline-flex text-sm font-semibold text-[var(--color-gold)]">{{ $div['cta'] }} →</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </x-container>
</section>

{{-- 5.4 Browse by property type --}}
<section class="fs-section fs-section-ivory" aria-labelledby="types-heading">
    <x-container>
        <x-section-heading
            title="Browse by property type"
            lead="Start with what you are looking for."
            heading-id="types-heading"
        />
        <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $homeTypes = \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->take(4)->get();
            @endphp
            @foreach($homeTypes as $homeType)
                @php
                    $href = $homeType->slug === 'hotel'
                        ? route('divisions.hospitality')
                        : route('properties', ['type' => $homeType->slug]);
                    $count = \App\Models\Listing::query()->published()->where('listing_type_id', $homeType->id)->count();
                @endphp
                <a href="{{ $href }}"
                   class="flex items-center gap-3 h-24 px-4 rounded-[var(--radius-card)] bg-white border border-[var(--color-border)] hover:border-[color-mix(in_srgb,var(--color-gold-dark)_40%,transparent)] transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]">
                    <span class="inline-flex items-center justify-center w-10 h-10 shrink-0 text-[var(--color-forest)]" aria-hidden="true">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $typeIcons[$homeType->slug] ?? $typeIcons['house'] }}"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-[var(--color-ink)]">{{ $homeType->name }}</span>
                        @if($count > 0)
                            <span class="block text-xs text-[var(--color-muted)] mt-0.5">{{ $count }} {{ \Illuminate\Support\Str::plural('listing', $count) }}</span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </x-container>
</section>

{{-- 5.5 Featured properties --}}
@if($featuredSale->isNotEmpty())
<section class="fs-section fs-section-ivory-50" aria-labelledby="featured-heading">
    <x-container>
        <x-section-heading
            title="Featured properties"
            lead="A selection of curated listings ready to explore."
            heading-id="featured-heading"
            action-href="{{ route('properties') }}"
            action-label="View all"
        />
        {{-- TODO owner: confirm verification claim — using "curated" until a real verification process is confirmed. --}}
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredSale as $house)
                <x-property-card :property="$house" />
            @endforeach
        </div>
    </x-container>
</section>
@endif

{{-- 5.6 Featured stays --}}
@if($featuredStays->isNotEmpty())
<section class="fs-section fs-section-ivory" aria-labelledby="stays-heading">
    <x-container>
        <x-section-heading
            title="Featured stays"
            lead="Short stays with the same care as a private residence."
            heading-id="stays-heading"
            action-href="{{ route('properties', ['purpose' => 'short_stay']) }}"
            action-label="View all"
        />
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredStays as $stay)
                <x-property-card :property="$stay" />
            @endforeach
        </div>
    </x-container>
</section>
@endif

{{-- 5.7 About + principles --}}
<section id="about" class="fs-section fs-section-ivory-50 scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="about-heading">
    <x-container class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-start">
        <div class="aspect-[4/5] w-full rounded-[var(--radius-card)] overflow-hidden order-2 lg:order-1">
            <img
                src="{{ $img['about']['src'] }}"
                alt="{{ $img['about']['alt'] }}"
                width="1200"
                height="1500"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover"
            >
        </div>
        <div class="order-1 lg:order-2">
            <x-section-heading
                eyebrow="Why Maskanoor"
                title="A quieter, more considered way to find a place."
                heading-id="about-heading"
            />
            <div class="mt-10 space-y-6">
                @foreach([
                    ['01', 'Thoughtful by design', 'Every detail — from materials to messaging — is chosen with care.'],
                    ['02', 'Personal by nature', 'We favour clarity and conversation over volume and noise.'],
                    ['03', 'International in outlook', 'A multi-country platform with local context at every step.'],
                ] as [$num, $label, $copy])
                    <div class="pt-5 border-t border-[var(--color-border)]">
                        <p class="font-[family-name:var(--font-display)] text-xl text-[var(--color-gold-dark)]">{{ $num }}</p>
                        <h3 class="mt-2 text-[var(--color-ink)]">{{ $label }}</h3>
                        <p class="mt-2 text-sm text-[var(--color-muted)] max-w-[48ch]">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-container>
</section>

{{-- 5.8 How we help --}}
<section id="services" class="fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="services-heading">
    <x-container>
        <x-section-heading title="How we help" heading-id="services-heading" />
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                [
                    'title' => 'Buying guidance',
                    'copy' => 'Shortlist homes that match your budget, lifestyle, and timeline.',
                    'href' => route('contact', ['enquiry' => 'buying']),
                    'icon' => 'M3 10l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z',
                ],
                [
                    'title' => 'Rental support',
                    'copy' => 'Compare apartments and residences with clear pricing and next steps.',
                    'href' => route('contact', ['enquiry' => 'rental']),
                    'icon' => 'M4 21V3h16v18M8 7h2M8 11h2M14 7h2M14 11h2',
                ],
                [
                    'title' => 'Listing help',
                    'copy' => 'Present your property with care to serious buyers and renters.',
                    'href' => route('contact', ['enquiry' => 'listing']),
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ],
            ] as $service)
                <div class="fs-card p-8 h-full flex flex-col">
                    <span class="inline-flex text-[var(--color-forest)]" aria-hidden="true">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $service['icon'] }}"/></svg>
                    </span>
                    <h3 class="mt-5">{{ $service['title'] }}</h3>
                    <p class="mt-2 text-sm text-[var(--color-muted)] flex-1">{{ $service['copy'] }}</p>
                    <a href="{{ $service['href'] }}" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">Talk to us →</a>
                </div>
            @endforeach
        </div>
    </x-container>
</section>

{{-- 5.9 Full-width image break --}}
<section class="relative w-full overflow-hidden" aria-label="Brand statement">
    <picture>
        <source media="(max-width: 767px)" srcset="{{ $img['break']['mobile'] }}">
        <img
            src="{{ $img['break']['desktop'] }}"
            alt="{{ $img['break']['alt'] }}"
            width="2400"
            height="1030"
            loading="lazy"
            decoding="async"
            class="w-full aspect-[4/5] md:aspect-[21/9] object-cover"
        >
    </picture>
    <div class="absolute inset-0 fs-break-overlay flex items-center justify-center px-6" aria-hidden="true"></div>
    <div class="absolute inset-0 flex items-center justify-center px-6">
                        <p
            class="fs-reveal font-[family-name:var(--font-display)] font-medium text-[var(--color-ivory)] text-center text-[clamp(28px,4vw,52px)] leading-tight max-w-[18ch]"
            x-data="revealOnView"
        >
            A quieter expression of luxury.
        </p>
    </div>
</section>

{{-- 5.10 Conditional: agents --}}
@if($agents->isNotEmpty())
<section id="agents" class="fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="agents-heading">
    <x-container>
        <x-section-heading title="Meet our agents" heading-id="agents-heading" />
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($agents->take(4) as $agent)
                <x-agent-card
                    :name="$agent->user->name"
                    :role="$agent->display_role"
                    :image="$agent->photo_url"
                    :listings="$agent->user->agent_listings_count ?? null"
                    :phone="$agent->phone"
                    :verified="$agent->is_verified || $agent->user?->isKycVerified()"
                    :rating="$agent->averageRating()"
                    href="#agents"
                />
            @endforeach
        </div>
    </x-container>
</section>
@endif

{{-- Testimonials --}}
@if($testimonials->isNotEmpty())
<section class="fs-section fs-section-ivory-50" aria-labelledby="testimonials-heading">
    <x-container>
        <x-section-heading title="What clients say" heading-id="testimonials-heading" />
        <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($testimonials->take(3) as $item)
                <x-testimonial-card :quote="$item->quote" :name="$item->name" :role="$item->role" />
            @endforeach
        </div>
    </x-container>
</section>
@endif

{{-- Blog --}}
@if($posts->isNotEmpty())
<section id="blog" class="fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="blog-heading">
    <x-container>
        <x-section-heading
            title="From the blog"
            heading-id="blog-heading"
            action-href="{{ route('blog.index') }}"
            action-label="View all"
        />
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($posts as $post)
                <x-blog-card
                    :title="$post->title"
                    :excerpt="$post->excerpt"
                    :image="$post->featured_image"
                    :date="optional($post->published_at)->toDateString()"
                    :category="$post->category"
                    :href="route('blog.show', $post->slug)"
                />
            @endforeach
        </div>
    </x-container>
</section>
@endif

{{-- 5.10 FAQ — always render --}}
<section id="faq" class="fs-section fs-section-ivory-50 scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="faq-heading">
    <x-container class="grid lg:grid-cols-12 gap-10 lg:gap-16">
        <div class="lg:col-span-4">
            <x-section-heading
                title="Frequently asked questions"
                lead="Quick answers before you reach out."
                heading-id="faq-heading"
            />
            {{-- TODO owner: confirm FAQ answers match real process and response times. --}}
        </div>
        <div class="lg:col-span-8 space-y-3">
            @foreach ($faqs as $index => $faq)
                <x-faq-item :question="$faq->question" :answer="$faq->answer" :open="$index === 0" />
            @endforeach
        </div>
    </x-container>
</section>

{{-- 5.11 CTA band --}}
<section class="fs-section-ivory pb-24 pt-8">
    <x-container>
        <div class="rounded-[var(--radius-card)] bg-[var(--color-forest-shadow)] px-6 py-12 md:px-12 md:py-14 border-t border-[color-mix(in_srgb,var(--color-gold)_40%,transparent)]">
            <p class="fs-eyebrow-on-dark">Maskanoor Group</p>
            <h2 class="mt-4 text-[var(--color-ivory)]">Let's shape what comes next.</h2>
            <p class="mt-3 text-[var(--color-sage)] max-w-[56ch]">
                Tell us whether you are exploring a stay, a property, or a potential partnership.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <x-button variant="primary" href="{{ route('contact') }}" surface="dark">Start a Conversation</x-button>
                <x-button variant="secondary" href="{{ route('properties') }}" surface="dark">Browse properties</x-button>
            </div>
        </div>
    </x-container>
</section>
@endsection
