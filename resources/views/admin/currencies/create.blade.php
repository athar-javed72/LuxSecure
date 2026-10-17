@extends('admin.layout')

@section('title', 'Add Currency')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Currencies', 'href' => route('admin.currencies.index')],
    ['label' => 'Add'],
]" />

<x-page-header title="Add currency" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.currencies.store') }}">
        @csrf
        @include('admin.currencies._form', ['currency' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create currency</x-button>
            <x-button variant="ghost" href="{{ route('admin.currencies.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
