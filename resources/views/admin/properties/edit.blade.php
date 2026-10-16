@extends('admin.layout')

@section('title', 'Edit Property')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Properties', 'href' => route('admin.properties.index')],
    ['label' => 'Edit'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Edit property</h1>
    <p class="mt-2 text-sm text-fs-secondary">Update {{ $property->title }}</p>
</div>
<div class="rounded-md bg-white shadow-sm border border-black/5 w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.properties.update', $property) }}" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        @include('admin.properties._form', ['property' => $property])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Update</x-button>
            <x-button variant="ghost" href="{{ route('admin.properties.index') }}">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
