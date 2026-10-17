@extends('admin.layout')

@section('title', 'Edit Organization')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Organizations', 'href' => route('admin.organizations.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit organization" :subtitle="$organization->name" />

<x-panel class="max-w-3xl">
    <form method="POST" action="{{ route('admin.organizations.update', $organization) }}">
        @csrf
        @method('PUT')
        @include('admin.organizations._form', ['organization' => $organization])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.organizations.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
