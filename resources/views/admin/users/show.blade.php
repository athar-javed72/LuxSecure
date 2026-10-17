@extends('admin.layout')

@section('title', 'User: ' . $user->name)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Users', 'href' => route('admin.users.index')],
    ['label' => $user->name],
]" />

<x-page-header title="User details">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.users.index') }}">Back</x-button>
    </x-slot:actions>
</x-page-header>

<x-panel class="w-full max-w-5xl">
    <h2 class="fs-display text-lg font-semibold text-fs-primary mb-6">{{ $user->name }}</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">ID</dt><dd class="mt-1">{{ $user->id }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Email</dt><dd class="mt-1">{{ $user->email }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Phone</dt><dd class="mt-1">{{ $user->phone ?? '—' }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Roles</dt><dd class="mt-1">{{ $user->getRoleNames()->implode(', ') ?: '—' }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Direct permissions</dt><dd class="mt-1">{{ $user->getDirectPermissions()->pluck('name')->implode(', ') ?: '—' }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Active</dt><dd class="mt-1"><x-status-badge :status="$user->is_active ? 'active' : 'inactive'">{{ $user->is_active ? 'Yes' : 'No' }}</x-status-badge></dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Verified</dt><dd class="mt-1">{{ $user->hasVerifiedEmail() ? 'Yes' : 'No' }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Registered</dt><dd class="mt-1">{{ $user->created_at->format('M j, Y H:i') }}</dd></div>
    </dl>
    <div class="mt-6 flex flex-wrap gap-2">
        @can('update', $user)
            <x-button variant="primary" href="{{ route('admin.users.edit', $user) }}">Edit</x-button>
        @endcan
        @can('delete', $user)
            <x-confirm-delete
                :action="route('admin.users.destroy', $user)"
                title="Delete this user?"
                message="“{{ $user->name }}” will be permanently removed."
            >
                <x-slot:trigger>
                    <x-button type="button" variant="ghost" class="!text-fs-danger">Delete</x-button>
                </x-slot:trigger>
            </x-confirm-delete>
        @endcan
    </div>
</x-panel>
@endsection
