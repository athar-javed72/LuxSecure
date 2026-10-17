@extends('admin.layout')

@section('title', 'Users')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Users'],
]" />

<x-page-header title="Users" subtitle="Search and manage accounts">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.users.export') }}">Export CSV</x-button>
        <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
        @can('create', App\Models\User::class)
            <x-button variant="primary" href="{{ route('admin.users.create') }}">Add user</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

<x-panel class="mb-6 max-w-5xl">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1 w-full">
            <x-input label="Search by email or name" name="search" :value="request('search')" placeholder="e.g. user@example.com" />
        </div>
        <x-button type="submit" variant="primary">Search</x-button>
        @if(request('search'))
            <x-button variant="ghost" href="{{ route('admin.users.index') }}">Clear</x-button>
        @endif
    </form>
</x-panel>

@if($users->isEmpty())
    <x-empty-state title="No users found" description="Try a different search or add a new user." action-label="Add user" :action-href="route('admin.users.create')" />
@else
    <x-table caption="Users" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Active</th>
                    <th>Registered</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td data-label="Name" class="font-medium text-fs-primary">{{ $user->name }}</td>
                        <td data-label="Email" class="text-fs-secondary">{{ $user->email }}</td>
                        <td data-label="Roles" class="text-fs-secondary">{{ $user->getRoleNames()->implode(', ') ?: '—' }}</td>
                        <td data-label="Active"><x-status-badge :status="$user->is_active ? 'active' : 'inactive'">{{ $user->is_active ? 'Yes' : 'No' }}</x-status-badge></td>
                        <td data-label="Registered" class="text-fs-secondary whitespace-nowrap">{{ $user->created_at->format('M j, Y') }}</td>
                        <td data-label="Actions">
                            <div class="flex flex-wrap justify-end gap-1">
                                <x-button variant="ghost" href="{{ route('admin.users.show', $user) }}" class="!px-3">View</x-button>
                                @can('update', $user)
                                    <x-button variant="secondary" href="{{ route('admin.users.edit', $user) }}" class="!px-3">Edit</x-button>
                                @endcan
                                @can('delete', $user)
                                    <x-confirm-delete
                                        :action="route('admin.users.destroy', $user)"
                                        title="Delete this user?"
                                        message="“{{ $user->name }}” will be permanently removed."
                                    >
                                        <x-slot:trigger>
                                            <x-button type="button" variant="ghost" class="!px-3 !text-fs-danger">Delete</x-button>
                                        </x-slot:trigger>
                                    </x-confirm-delete>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif

@if($users->hasPages())
    <div class="mt-4">{{ $users->links() }}</div>
@endif
@endsection
