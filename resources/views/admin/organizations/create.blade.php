@extends('admin.layout')

@section('title', 'Add Organization')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Organizations', 'href' => route('admin.organizations.index')],
    ['label' => 'Add'],
]" />

<x-page-header title="Add organization" />

<x-panel class="max-w-3xl">
    <form method="POST" action="{{ route('admin.organizations.store') }}">
        @csrf
        @include('admin.organizations._form', ['organization' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create organization</x-button>
            <x-button variant="ghost" href="{{ route('admin.organizations.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
