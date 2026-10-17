@extends('admin.layout')

@section('title', 'Edit Currency')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Currencies', 'href' => route('admin.currencies.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit currency" :subtitle="$currency->code" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.currencies.update', $currency) }}">
        @csrf
        @method('PUT')
        @include('admin.currencies._form', ['currency' => $currency])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.currencies.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
