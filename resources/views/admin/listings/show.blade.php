@extends('admin.layout')

@section('title', $listing->title)

@section('content')
@php use App\Support\Media; @endphp
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listings', 'href' => route('admin.listings.index')],
    ['label' => $listing->title],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Listing details</h1>
    <div class="flex flex-wrap gap-2">
        <x-button variant="secondary" href="{{ route('admin.listings.edit', $listing) }}">Edit</x-button>
        <x-button variant="ghost" href="{{ route('admin.listings.index') }}">Back</x-button>
    </div>
</div>

<div class="rounded-md bg-white shadow-sm border border-black/5 p-6 w-full max-w-5xl">
    <h2 class="text-lg font-bold text-fs-primary mb-4">{{ $listing->title }}</h2>
    @if(filled($listing->primary_image_url))
        <div class="aspect-[4/3] rounded-md overflow-hidden bg-fs-surface mb-4">
            <img src="{{ $listing->primary_image_url }}" alt="{{ $listing->title }}" loading="lazy" width="800" height="600" class="w-full h-full object-cover">
        </div>
    @endif
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-xs text-fs-muted">Location</dt><dd class="mt-1 text-fs-primary">{{ $listing->location }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Type</dt><dd class="mt-1 text-fs-primary">{{ $listing->listingType?->name }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Purpose</dt><dd class="mt-1 text-fs-primary">{{ ucfirst(str_replace('_', ' ', $listing->purpose)) }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Price</dt><dd class="mt-1 text-fs-primary">{{ $listing->formatted_price }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Status</dt><dd class="mt-1"><x-badge :tone="$listing->status === 'published' ? 'success' : 'muted'">{{ ucfirst($listing->status) }}</x-badge></dd></div>
        <div><dt class="text-xs text-fs-muted">City</dt><dd class="mt-1 text-fs-primary">{{ $listing->city?->name ?? '—' }}</dd></div>
    </dl>
    @if(!empty($listing->attributeValues()))
        <div class="mt-6">
            <h3 class="text-sm font-semibold text-fs-primary mb-2">Attributes</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                @foreach($listing->attributeValues() as $key => $value)
                    <div>
                        <dt class="text-xs text-fs-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="mt-1 text-fs-primary">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif
    @if($listing->description)
        <p class="mt-6 text-sm text-fs-secondary">{{ $listing->description }}</p>
    @endif
</div>
@endsection
