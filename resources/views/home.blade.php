@extends('include.master')

@section('title', 'Find Your Dream Home')
@section('meta_description', 'LuxSecure — Browse verified homes, villas, and apartments. Search by location, type, and budget.')

@section('home_content')
{{-- Hero: fits one 1920×950 viewport with search visible --}}
<section class="relative w-full h-[min(900px,calc(100vh-72px))] flex items-center overflow-hidden">
    <img
        src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80"
        alt="Modern home exterior"
        width="1920"
        height="1080"
        class="absolute inset-0 w-full h-full object-cover"
    >
    <div class="absolute inset-0 bg-gradient-to-r from-fs-primary/85 via-fs-primary/65 to-fs-primary/40"></div>

    <x-container class="relative z-10 w-full py-8 md:py-12">
        <div class="max-w-prose">
            <h1 class="text-hero md:text-2xl font-bold text-white">
                Secure Your Dream Property
            </h1>
            <p class="mt-4 text-base text-slate-200 max-w-prose">
                Enterprise-grade security meets curated luxury. Verified listings across Pakistan.
            </p>
        </div>

        <form action="{{ route('properties') }}" method="GET" class="mt-6 md:mt-8 w-full max-w-5xl mx-auto bg-white rounded-md shadow-lg p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
                <x-input label="Location" name="search" :value="request('search')" placeholder="City or area" />
                <x-select
                    label="Property Type"
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
                <div class="flex flex-col">
                    <span class="block text-xs font-semibold text-fs-primary mb-1 opacity-0 select-none" aria-hidden="true">Search</span>
                    <x-button type="submit" variant="primary" class="w-full !h-12">Search</x-button>
                </div>
            </div>
        </form>
    </x-container>
</section>

{{-- Property type tiles --}}
<section class="py-16" aria-labelledby="types-heading">
    <x-container>
        <x-section-heading title="Browse by property type" subtitle="Equal tiles for every category" heading-id="types-heading" />
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $typeTiles = [
                    'apartment' => 'M4 21V3h16v18M8 7h2M8 11h2M8 15h2M14 7h2M14 11h2M14 15h2',
                    'plot' => 'M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4',
                    'hotel' => 'M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6',
                    'house' => 'M3 10l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z',
                ];
                $homeTypes = \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->take(4)->get();
            @endphp
            @forelse($homeTypes as $homeType)
                <a href="{{ route('properties', ['type' => $homeType->slug]) }}"
                   class="w-full flex flex-col items-center justify-center gap-3 h-32 rounded-md bg-white shadow-sm hover:shadow-lg transition-shadow duration-150 border border-black/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-sm bg-fs-brand-light text-fs-brand">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $typeTiles[$homeType->slug] ?? $typeTiles['house'] }}"/></svg>
                    </span>
                    <span class="text-sm font-semibold text-fs-primary">{{ $homeType->name }}</span>
                </a>
            @empty
                <p class="col-span-full text-sm text-fs-muted">Listing types will appear here once configured.</p>
            @endforelse
        </div>
    </x-container>
</section>

{{-- Featured properties --}}
<section class="pb-16" aria-labelledby="featured-heading">
    <x-container>
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <x-section-heading title="Featured properties" subtitle="Handpicked listings ready to explore" heading-id="featured-heading" />
            <x-button variant="ghost" href="{{ route('properties') }}">View all</x-button>
        </div>
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($houses->take(8) as $house)
                <x-property-card :property="$house" />
            @empty
                <p class="col-span-full text-sm text-fs-secondary max-w-prose">No properties available yet.</p>
            @endforelse
        </div>
    </x-container>
</section>

{{-- About --}}
<section id="about" class="bg-slate-50 py-16 scroll-mt-[72px]" aria-labelledby="about-heading">
    <x-container class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <x-section-heading title="About LuxSecure" subtitle="A cleaner way to discover verified real estate" heading-id="about-heading" />
            <p class="mt-6 text-sm text-fs-secondary max-w-prose">
                LuxSecure helps buyers and renters explore curated homes with clear pricing, trusted agents, and a responsive experience on every device.
            </p>
            <ul class="mt-6 space-y-3 text-sm text-fs-secondary max-w-prose">
                <li class="flex gap-2"><span class="text-fs-brand font-bold">✓</span> Verified inventory across major Pakistani cities</li>
                <li class="flex gap-2"><span class="text-fs-brand font-bold">✓</span> Secure inquiries and saved favorites</li>
                <li class="flex gap-2"><span class="text-fs-brand font-bold">✓</span> Agents who understand local markets</li>
            </ul>
        </div>
        <div class="aspect-[4/3] w-full rounded-md overflow-hidden shadow-sm">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80" alt="Bright living room interior" loading="lazy" width="1200" height="900" class="w-full h-full object-cover">
        </div>
    </x-container>
</section>

{{-- Services --}}
<section id="services" class="py-16 scroll-mt-[72px]" aria-labelledby="services-heading">
    <x-container>
        <x-section-heading title="Our services" subtitle="Support from search to closing" heading-id="services-heading" align="center" class="mx-auto" />
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['Buying guidance', 'Shortlist homes that match your budget, lifestyle, and timeline.'],
                ['Rental support', 'Compare apartments and negotiate with confidence.'],
                ['Listing help', 'Get your property in front of serious buyers and renters.'],
            ] as [$title, $copy])
                <div class="rounded-md bg-white shadow-sm p-6 border border-slate-100">
                    <h3 class="text-base font-semibold text-fs-primary">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-fs-secondary max-w-prose">{{ $copy }}</p>
                </div>
            @endforeach
        </div>
    </x-container>
</section>

{{-- Agents --}}
<section id="agents" class="bg-slate-50 py-16 scroll-mt-[72px]" aria-labelledby="agents-heading">
    <x-container>
        <x-section-heading title="Meet our agents" subtitle="Local experts ready to help" heading-id="agents-heading" />
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($agents as $agent)
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
            @empty
                <p class="text-sm text-fs-muted col-span-full">Agent profiles will appear here once published.</p>
            @endforelse
        </div>
    </x-container>
</section>

{{-- Testimonials --}}
<section class="py-16" aria-labelledby="testimonials-heading" x-data="{ slide: 0 }">
    <x-container>
        <x-section-heading title="What clients say" subtitle="Trusted by buyers and investors" heading-id="testimonials-heading" align="center" class="mx-auto" />
        @if($testimonials->isNotEmpty())
            <div class="mt-6 hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($testimonials->take(3) as $item)
                    <x-testimonial-card :quote="$item->quote" :name="$item->name" :role="$item->role" />
                @endforeach
            </div>
            <div class="mt-6 md:hidden">
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-200" :style="`transform: translateX(-${slide * 100}%)`">
                        @foreach ($testimonials as $item)
                            <div class="w-full shrink-0 px-1"><x-testimonial-card :quote="$item->quote" :name="$item->name" :role="$item->role" /></div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4 flex justify-center gap-2">
                    @foreach ($testimonials as $index => $item)
                        <button type="button" class="inline-flex items-center justify-center w-11 h-11" @click="slide = {{ $index }}" aria-label="Show testimonial {{ $index + 1 }}">
                            <span class="w-2.5 h-2.5 rounded-full" :class="slide === {{ $index }} ? 'bg-fs-brand' : 'bg-slate-300'"></span>
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <p class="mt-6 text-center text-sm text-fs-muted">Client stories coming soon.</p>
        @endif
    </x-container>
</section>

{{-- Blog --}}
<section id="blog" class="bg-slate-50 py-16 scroll-mt-[72px]" aria-labelledby="blog-heading">
    <x-container>
        <x-section-heading title="From the blog" subtitle="Market tips and neighborhood guides" heading-id="blog-heading" />
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($posts as $post)
                <x-blog-card
                    :title="$post->title"
                    :excerpt="$post->excerpt"
                    :image="$post->featured_image ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80'"
                    :date="optional($post->published_at)->toDateString()"
                    :category="$post->category"
                    :href="route('blog.show', $post->slug)"
                />
            @empty
                <p class="text-sm text-fs-muted col-span-full">No blog posts published yet.</p>
            @endforelse
        </div>
        @if($posts->isNotEmpty())
            <div class="mt-6"><x-button variant="ghost" href="{{ route('blog.index') }}">View all posts</x-button></div>
        @endif
    </x-container>
</section>

{{-- FAQ --}}
<section class="py-16" aria-labelledby="faq-heading">
    <x-container>
        <x-section-heading title="Frequently asked questions" subtitle="Quick answers before you reach out" heading-id="faq-heading" />
        <div class="mt-6 max-w-3xl space-y-3">
            @forelse ($faqs as $index => $faq)
                <x-faq-item :question="$faq->question" :answer="$faq->answer" :open="$index === 0" />
            @empty
                <p class="text-sm text-fs-muted">FAQs will appear here once published.</p>
            @endforelse
        </div>
    </x-container>
</section>

{{-- CTA --}}
<section class="pb-16">
    <x-container>
        <div class="rounded-md bg-fs-primary px-6 py-12 text-center text-white border border-white/5 shadow-lg">
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-fs-brand">LuxSecure</p>
            <h2 class="mt-3 text-xl md:text-2xl font-bold">Ready to find your next property?</h2>
            <p class="mt-3 text-sm text-slate-300 max-w-prose mx-auto">Browse verified listings or talk to a LuxSecure agent today.</p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <x-button variant="primary" href="{{ route('properties') }}">Browse properties</x-button>
                <x-button variant="ghost" href="{{ route('contact') }}" class="!border-white/30 !text-white hover:!bg-white/10">Contact us</x-button>
            </div>
        </div>
    </x-container>
</section>
@endsection
