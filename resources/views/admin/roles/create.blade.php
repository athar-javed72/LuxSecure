@extends('admin.layout')

@section('title', 'Create Role')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Roles', 'href' => route('admin.roles.index')],
    ['label' => 'Create'],
]" />

<x-page-header title="Create role" />

<x-panel class="w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <x-input label="Name *" name="name" :value="old('name')" placeholder="e.g. regional_manager" required />
        <div class="mt-6">
            <h2 class="text-sm font-semibold text-fs-primary mb-3">Permissions</h2>
            @include('admin.roles._permission-matrix', ['selected' => []])
        </div>
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create role</x-button>
            <x-button variant="ghost" href="{{ route('admin.roles.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
