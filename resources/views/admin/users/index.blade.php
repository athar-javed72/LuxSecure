@extends('admin.layout')

@section('title', 'Users')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Users'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Users</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Search and manage accounts</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <x-button variant="ghost" href="{{ route('admin.users.export') }}">Export CSV</x-button>
        <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
        @can('create', App\Models\User::class)
            <x-button variant="primary" href="{{ route('admin.users.create') }}">Add user</x-button>
        @endcan
    </div>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 rounded-md bg-white shadow-sm border border-black/5 p-4 flex flex-col sm:flex-row gap-3 items-end w-full max-w-5xl">
    <div class="flex-1 w-full">
        <x-input label="Search by email or name" name="search" :value="request('search')" placeholder="e.g. user@example.com" />
    </div>
    <x-button type="submit" variant="primary">Search</x-button>
    @if(request('search'))
        <x-button variant="ghost" href="{{ route('admin.users.index') }}">Clear</x-button>
    @endif
</form>

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold">Name</th>
                    <th class="px-3 py-3 font-semibold">Email</th>
                    <th class="px-3 py-3 font-semibold">Roles</th>
                    <th class="px-3 py-3 font-semibold">Active</th>
                    <th class="px-3 py-3 font-semibold">Registered</th>
                    <th class="px-3 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($users as $user)
                    <tr>
                        <td class="px-3 py-3 font-medium text-fs-primary">{{ $user->name }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $user->email }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $user->getRoleNames()->implode(', ') ?: '—' }}</td>
                        <td class="px-3 py-3"><x-badge :tone="$user->is_active ? 'success' : 'muted'">{{ $user->is_active ? 'Yes' : 'No' }}</x-badge></td>
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-3 py-3">
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
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($users->hasPages())
    <div class="mt-4">{{ $users->links() }}</div>
@endif
@endsection
