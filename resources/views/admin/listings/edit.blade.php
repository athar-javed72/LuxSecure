@extends('admin.layout')

@section('title', 'Edit Listing')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listings', 'href' => route('admin.listings.index')],
    ['label' => 'Edit'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Edit listing</h1>
    <p class="mt-2 text-sm text-fs-secondary">{{ $listing->title }}</p>
</div>
<div class="rounded-md bg-white shadow-sm border border-black/5 w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.listings.update', $listing) }}" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        @include('admin.listings._form', ['listing' => $listing])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.listings.index') }}">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
