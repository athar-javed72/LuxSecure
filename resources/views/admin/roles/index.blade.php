@extends('admin.layout')

@section('title', 'Roles')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Roles'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Roles</h1>
        <p class="mt-2 text-sm text-fs-secondary">Manage roles and their permission matrices</p>
    </div>
    @can('create', App\Models\Role::class)
        <x-button variant="primary" href="{{ route('admin.roles.create') }}">Add role</x-button>
    @endcan
</div>

@if(session('error'))
    <div class="mb-4 p-4 rounded-sm bg-red-50 text-fs-danger text-sm">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">Users</th>
                <th class="px-3 py-3 font-semibold">Permissions</th>
                <th class="px-3 py-3 font-semibold">System</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($roles as $role)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $role->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $role->users_count }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $role->permissions_count }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$role->is_system ? 'muted' : 'success'">{{ $role->is_system ? 'System' : 'Custom' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            @can('managePermissions', $role)
                                <x-button variant="secondary" href="{{ route('admin.roles.matrix', $role) }}" class="!px-3">Matrix</x-button>
                            @endcan
                            @can('update', $role)
                                <x-button variant="ghost" href="{{ route('admin.roles.edit', $role) }}" class="!px-3">Edit</x-button>
                            @endcan
                            @can('delete', $role)
                                <x-confirm-delete :action="route('admin.roles.destroy', $role)" label="Delete" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-fs-muted">No roles found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($roles->hasPages())
    <div class="mt-4">{{ $roles->links() }}</div>
@endif
@endsection
