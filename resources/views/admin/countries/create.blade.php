@extends('admin.layout')

@section('title', 'Add Country')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Countries', 'href' => route('admin.countries.index')],
    ['label' => 'Add'],
]" />

<x-page-header title="Add country" />

<x-panel class="max-w-4xl">
    <form method="POST" action="{{ route('admin.countries.store') }}">
        @csrf
        @include('admin.countries._form', ['country' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create country</x-button>
            <x-button variant="ghost" href="{{ route('admin.countries.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
