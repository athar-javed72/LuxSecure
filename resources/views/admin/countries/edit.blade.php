@extends('admin.layout')

@section('title', 'Edit Country')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Countries', 'href' => route('admin.countries.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit country" :subtitle="$country->name" />

<x-panel class="max-w-4xl">
    <form method="POST" action="{{ route('admin.countries.update', $country) }}">
        @csrf
        @method('PUT')
        @include('admin.countries._form', ['country' => $country])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.countries.index') }}">Cancel</x-button>
            <x-button variant="secondary" href="{{ route('admin.countries.settings', $country) }}">Settings</x-button>
        </div>
    </form>
</x-panel>
@endsection
