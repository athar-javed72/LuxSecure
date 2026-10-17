@extends('admin.layout')

@section('title', 'Add Listing Type')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listing Types', 'href' => route('admin.listing-types.index')],
    ['label' => 'Add'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Add listing type</h1>
</div>
<div class="rounded-md bg-white shadow-sm border border-black/5 w-full max-w-4xl p-6">
    <form method="POST" action="{{ route('admin.listing-types.store') }}">
        @csrf
        @include('admin.listing-types._form', ['listingType' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create type</x-button>
            <x-button variant="ghost" href="{{ route('admin.listing-types.index') }}">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
