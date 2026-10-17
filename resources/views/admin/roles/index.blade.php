@extends('admin.layout')

@section('title', 'Roles')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Roles'],
]" />

<x-page-header title="Roles" subtitle="Manage roles and their permission matrices">
    <x-slot:actions>
        @can('create', App\Models\Role::class)
            <x-button variant="primary" href="{{ route('admin.roles.create') }}">Add role</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

@if($roles->isEmpty())
    <x-empty-state title="No roles found" action-label="Add role" :action-href="route('admin.roles.create')" />
@else
    <x-table caption="Roles" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Name</th>
                    <th>Users</th>
                    <th>Permissions</th>
                    <th>System</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td data-label="Name" class="font-medium text-fs-primary">{{ $role->name }}</td>
                        <td data-label="Users" class="text-fs-secondary">{{ $role->users_count }}</td>
                        <td data-label="Permissions" class="text-fs-secondary">{{ $role->permissions_count }}</td>
                        <td data-label="System"><x-badge :tone="$role->is_system ? 'muted' : 'success'">{{ $role->is_system ? 'System' : 'Custom' }}</x-badge></td>
                        <td data-label="Actions" class="text-right">
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
                @endforeach
            </tbody>
        </x-table>
@endif

@if($roles->hasPages())
    <div class="mt-4">{{ $roles->links() }}</div>
@endif
@endsection
