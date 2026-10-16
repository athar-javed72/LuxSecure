@extends('admin.layout')

@section('title', $property->title)

@section('content')
@php use App\Support\Media; @endphp
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Properties', 'href' => route('admin.properties.index')],
    ['label' => $property->title],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Property details</h1>
    <x-button variant="ghost" href="{{ route('admin.properties.index') }}">Back</x-button>
</div>

<div class="rounded-md bg-white shadow-sm border border-black/5 p-6 w-full max-w-5xl">
    <h2 class="text-lg font-bold text-fs-primary mb-4">{{ $property->title }}</h2>
    @if(filled($property->primary_image_url))
        <div class="aspect-[4/3] rounded-md overflow-hidden bg-fs-surface mb-4">
            <img src="{{ $property->primary_image_url }}" alt="{{ $property->title }}" loading="lazy" width="800" height="600" class="w-full h-full object-cover">
        </div>
    @endif
    @if($property->images->isNotEmpty())
        <div class="mb-4 flex flex-wrap gap-2">
            @foreach($property->images as $image)
                @php $url = Media::url($image->path); @endphp
                @if($url !== '')
                    <img src="{{ $url }}" alt="Gallery image {{ $loop->iteration }} for {{ $property->title }}" width="120" height="90" class="w-[120px] h-[90px] object-cover rounded-sm border border-black/5">
                @endif
            @endforeach
        </div>
    @endif
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-xs text-fs-muted">Location</dt><dd class="mt-1 text-fs-primary">{{ $property->location }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Type</dt><dd class="mt-1 text-fs-primary">{{ $property->type }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Price</dt><dd class="mt-1 text-fs-primary">PKR {{ number_format($property->price / 1_00_00_000, 1) }} Crore</dd></div>
        <div><dt class="text-xs text-fs-muted">Status</dt><dd class="mt-1"><x-badge :tone="$property->is_active ? 'success' : 'muted'">{{ $property->is_active ? 'Active' : 'Inactive' }}</x-badge></dd></div>
    </dl>
    @if($property->description)
        <p class="mt-4 text-sm text-fs-secondary">{{ $property->description }}</p>
    @endif
    <div class="mt-6">
        <x-button variant="primary" href="{{ route('admin.properties.edit', $property) }}">Edit</x-button>
    </div>
</div>
@endsection
