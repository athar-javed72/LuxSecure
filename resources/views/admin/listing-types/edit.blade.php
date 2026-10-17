@extends('admin.layout')

@section('title', 'Edit Listing Type')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listing Types', 'href' => route('admin.listing-types.index')],
    ['label' => 'Edit'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Edit listing type</h1>
    <p class="mt-2 text-sm text-fs-secondary">{{ $listingType->name }}</p>
</div>
<div class="rounded-md bg-white shadow-sm border border-black/5 w-full max-w-4xl p-6">
    <form method="POST" action="{{ route('admin.listing-types.update', $listingType) }}">
        @csrf
        @method('PUT')
        @include('admin.listing-types._form', ['listingType' => $listingType])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.listing-types.index') }}">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
