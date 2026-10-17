@extends('include.master')

@section('title', $property->title)
@section('meta_description', Str::limit(strip_tags($property->description ?: $property->title . ' in ' . $property->location), 150))

@section('content')
@php
    $listing = $property->purpose ?? 'sale';
    $gallery = $property->images;
    $mainImage = $property->primary_image_url;
    $isFav = auth()->check() ? auth()->user()->favorites()->where('listing_id', $property->id)->exists() : false;
@endphp

<x-container class="py-8">
    <nav class="mb-6 text-sm text-fs-secondary" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-fs-brand transition-colors duration-150">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('properties') }}" class="hover:text-fs-brand transition-colors duration-150">Properties</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-fs-primary font-medium truncate max-w-[220px]" aria-current="page">{{ $property->title }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-8 space-y-6">
            <div class="relative aspect-[4/3] rounded-md overflow-hidden bg-slate-100 shadow-sm">
                @if(filled($mainImage))
                    <img src="{{ $mainImage }}" alt="{{ $property->title }}" width="1200" height="900" class="w-full h-full object-cover" data-img-fallback>
                    <div data-img-placeholder class="hidden absolute inset-0 flex items-center justify-center bg-slate-200 text-fs-muted">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                    </div>
                @else
                    <div class="absolute inset-0 flex items-center justify-center bg-slate-200 text-fs-muted">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                    </div>
                @endif
                <div class="absolute top-4 left-4">
                    <x-badge :tone="$listing === 'rent' || $listing === 'short_stay' ? 'rent' : 'sale'">
                        {{ $listing === 'rent' ? 'For Rent' : ($listing === 'short_stay' ? 'Short Stay' : 'For Sale') }}
                    </x-badge>
                </div>
                @auth
                    <form action="{{ route('favorites.toggle', $property) }}" method="POST" class="absolute top-4 right-4">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-white shadow-sm text-fs-secondary hover:text-fs-danger transition-colors duration-150" aria-label="Toggle wishlist">
                            <svg class="w-5 h-5 {{ $isFav ? 'fill-fs-danger text-fs-danger' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>
                        </button>
                    </form>
                @endauth
            </div>

            @if($gallery->count() > 1)
                <div class="flex gap-3 overflow-x-auto fs-hide-scrollbar pb-1">
                    @foreach($gallery as $img)
                        <img
                            src="{{ asset('storage/' . $img->path) }}"
                            alt="{{ $property->title }} gallery image"
                            loading="lazy"
                            width="160"
                            height="120"
                            class="h-24 w-32 object-cover rounded-sm shrink-0 border border-slate-100"
                        >
                    @endforeach
                </div>
            @endif

            <div class="rounded-md bg-white shadow-sm border border-slate-100 p-6">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <x-badge tone="muted">{{ $property->type_name }}</x-badge>
                </div>
                <h1 class="text-xl md:text-2xl font-bold text-fs-primary">{{ $property->title }}</h1>
                <p class="mt-2 text-sm text-fs-muted flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $property->location }}
                </p>
                <p class="mt-4 text-lg font-bold text-fs-primary">PKR {{ number_format($property->price / 1_00_00_000, 1) }} Crore</p>
                @if(($averageRating ?? null) !== null)
                    <p class="mt-2 text-sm text-fs-secondary">Average rating: <span class="font-semibold text-fs-primary">{{ number_format($averageRating, 1) }}</span>/5 ({{ $reviews->count() }} reviews)</p>
                @endif

                <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-3 gap-4 text-sm text-fs-secondary">
                    <div><span class="block text-xs text-fs-muted mb-1">Beds</span>{{ $property->bedrooms ?? '—' }}</div>
                    <div><span class="block text-xs text-fs-muted mb-1">Baths</span>{{ $property->bathrooms ?? '—' }}</div>
                    <div><span class="block text-xs text-fs-muted mb-1">Type</span>{{ $property->type_name }}</div>
                </div>

                @if($property->description)
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h2 class="text-base font-semibold text-fs-primary mb-2">About this property</h2>
                        <p class="text-sm text-fs-secondary leading-relaxed max-w-prose">{{ $property->description }}</p>
                    </div>
                @endif

                @if($property->video_url || $property->floor_plan_path)
                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-2 text-sm">
                        <h2 class="text-base font-semibold text-fs-primary mb-2">Media</h2>
                        @if($property->video_url)
                            <p><a href="{{ $property->video_url }}" class="text-fs-brand hover:underline" target="_blank" rel="noopener">Watch video tour</a></p>
                        @endif
                        @if($property->floor_plan_path)
                            <p><a href="{{ \App\Support\StorageDisk::url($property->floor_plan_path) }}" class="text-fs-brand hover:underline" target="_blank" rel="noopener">View floor plan</a></p>
                        @endif
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-black/5">
                    <h2 class="fs-display text-lg text-fs-primary mb-4">Reviews</h2>
                    <div class="space-y-4">
                        @forelse($reviews as $review)
                            <div class="text-sm border-b border-black/5 pb-4 last:border-0 last:pb-0">
                                <p class="font-medium text-fs-primary">{{ $review->user?->name }} · {{ $review->rating }}/5</p>
                                <p class="text-fs-secondary mt-1">{{ $review->body }}</p>
                            </div>
                        @empty
                            <x-empty-state
                                title="No reviews yet"
                                description="Be the first to share your experience with this property."
                            />
                        @endforelse
                    </div>
                    @auth
                        <x-panel class="mt-6 max-w-md !p-4 sm:!p-5">
                            <h3 class="text-sm font-semibold text-fs-primary">Write a review</h3>
                            <form method="POST" action="{{ route('reviews.store', $property) }}" class="mt-3 space-y-3">
                                @csrf
                                <x-input label="Rating (1–5)" name="rating" type="number" min="1" max="5" :value="old('rating')" required />
                                <div class="w-full">
                                    <label for="review-body" class="block text-xs font-semibold text-fs-primary mb-1">Review</label>
                                    <textarea
                                        id="review-body"
                                        name="body"
                                        rows="3"
                                        required
                                        class="w-full min-h-[5rem] px-4 py-3 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary placeholder:text-fs-muted transition-colors duration-150 focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                                    >{{ old('body') }}</textarea>
                                    @error('body')
                                        <p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <x-button type="submit" variant="primary">Submit review</x-button>
                            </form>
                        </x-panel>
                    @endauth
                </div>
            </div>
        </div>

        <aside class="lg:col-span-4 space-y-4 lg:sticky lg:top-24">
            @php
                $agentUser = $property->agent;
                $agentProfile = $agentUser?->agentProfile;
            @endphp
            @if($agentUser)
                <div class="rounded-md bg-white shadow-sm border border-slate-100 p-6">
                    <div class="flex items-center gap-3">
                        <img src="{{ $agentProfile?->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($agentUser->name).'&background=0B1C2C&color=C9A227' }}" alt="{{ $agentUser->name }}" loading="lazy" width="56" height="56" class="w-14 h-14 rounded-full object-cover">
                        <div>
                            <h2 class="text-base font-semibold text-fs-primary flex items-center gap-2">
                                {{ $agentUser->name }}
                                @if($agentProfile?->is_verified || $agentUser->isKycVerified())
                                    <span class="text-[10px] uppercase tracking-wide text-fs-brand">Verified</span>
                                @endif
                            </h2>
                            <p class="text-sm text-fs-secondary">{{ $agentProfile?->display_role ?? 'LuxSecure Agent' }}</p>
                            @if(($agentAverageRating ?? null) !== null)
                                <p class="text-xs text-fs-muted mt-1">Agent rating {{ number_format($agentAverageRating, 1) }}/5</p>
                            @endif
                        </div>
                    </div>
                    @if($agentProfile?->phone)
                        <p class="mt-4 text-sm text-fs-secondary">{{ $agentProfile->phone }}</p>
                    @elseif($agentUser->phone)
                        <p class="mt-4 text-sm text-fs-secondary">{{ $agentUser->phone }}</p>
                    @endif
                    @if($agentProfile?->whatsapp)
                        <p class="mt-1 text-sm"><a class="text-fs-brand hover:underline" href="https://wa.me/{{ preg_replace('/\D+/', '', $agentProfile->whatsapp) }}" target="_blank" rel="noopener">WhatsApp</a></p>
                    @endif
                    @if($agentProfile?->bio)
                        <p class="mt-3 text-sm text-fs-secondary">{{ Str::limit($agentProfile->bio, 140) }}</p>
                    @endif
                    <div class="mt-4">
                        <x-button variant="ghost" href="{{ route('home') }}#agents" class="w-full">View agents</x-button>
                    </div>
                </div>
            @endif

            @if(($property->purpose === 'short_stay' || $property->roomTypes->isNotEmpty()) && $property->roomTypes->where('is_active', true)->isNotEmpty())
                <div class="rounded-md bg-white shadow-sm border border-slate-100 p-6">
                    <h2 class="text-base font-semibold text-fs-primary">Book a stay</h2>
                    <p class="mt-2 text-sm text-fs-secondary">Check availability and reserve nights online.</p>
                    <div class="mt-4">
                        @auth
                            <x-button variant="primary" href="{{ route('bookings.create', $property) }}" class="w-full">Book now</x-button>
                        @else
                            <x-button variant="primary" href="{{ route('login.form') }}" class="w-full">Sign in to book</x-button>
                        @endauth
                    </div>
                </div>
            @endif

            <div class="rounded-md bg-white shadow-lg border border-slate-100 p-6">
                <h2 class="text-base font-semibold text-fs-primary">Send an inquiry</h2>
                <p class="mt-2 text-sm text-fs-secondary">Ask about availability, viewing times, or financing.</p>
                <form method="POST" action="{{ route('contact.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="listing_id" value="{{ $property->id }}">
                    @error('listing_id')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
                    <x-input label="Name" name="name" :value="old('name', auth()->user()->name ?? '')" required />
                    <x-input label="Email" name="email" type="email" :value="old('email', auth()->user()->email ?? '')" required />
                    <div>
                        <label for="message" class="block text-xs font-semibold text-fs-primary mb-1">Message</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full px-4 py-3 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary placeholder:text-fs-muted focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('message', 'I am interested in '.$property->title.' ('.$property->location.'). Please contact me.') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
                    </div>
                    <x-button type="submit" variant="primary" class="w-full">Request info</x-button>
                </form>
            </div>
        </aside>
    </div>

    <div class="mt-8">
        <x-button variant="ghost" href="{{ route('properties') }}">Back to listings</x-button>
    </div>
</x-container>
@endsection
