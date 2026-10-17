@extends('admin.layout')

@section('title', 'Edit Role')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Roles', 'href' => route('admin.roles.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit role" :subtitle="$role->name" />

<x-panel class="w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')
        <x-input label="Name *" name="name" :value="old('name', $role->name)" :required="!$role->is_system" :disabled="$role->is_system" />
        @if($role->is_system)
            <input type="hidden" name="name" value="{{ $role->name }}">
            <p class="mt-1 text-xs text-fs-muted">System role names cannot be changed.</p>
        @endif
        <div class="mt-6">
            <h2 class="text-sm font-semibold text-fs-primary mb-3">Permissions</h2>
            @include('admin.roles._permission-matrix', ['selected' => $selected])
        </div>
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save role</x-button>
            <x-button variant="ghost" href="{{ route('admin.roles.index') }}">Cancel</x-button>
            <x-button variant="secondary" href="{{ route('admin.roles.matrix', $role) }}">Open matrix</x-button>
        </div>
    </form>
</x-panel>
@endsection
