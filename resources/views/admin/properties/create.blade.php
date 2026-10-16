@extends('admin.layout')

@section('title', 'Add Property')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Properties', 'href' => route('admin.properties.index')],
    ['label' => 'Add'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Add property</h1>
    <p class="mt-2 text-sm text-fs-secondary">Create a new property listing</p>
</div>
<div class="rounded-md bg-white shadow-sm border border-black/5 w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        @include('admin.properties._form', ['property' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create property</x-button>
            <x-button variant="ghost" href="{{ route('admin.properties.index') }}">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
