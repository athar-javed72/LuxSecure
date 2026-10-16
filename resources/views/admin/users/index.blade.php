@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Users</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Search and manage accounts</p>
    </div>
    <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 rounded-md bg-white shadow-sm border border-slate-100 p-4 flex flex-col sm:flex-row gap-3 items-end w-full max-w-5xl">
    <div class="flex-1 w-full">
        <x-input label="Search by email or name" name="search" :value="request('search')" placeholder="e.g. user@example.com" />
    </div>
    <x-button type="submit" variant="primary">Search</x-button>
    @if(request('search'))
        <x-button variant="ghost" href="{{ route('admin.users.index') }}">Clear</x-button>
    @endif
</form>

<div class="rounded-md bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto xl:overflow-x-visible">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold xl:w-[6%]">#</th>
                    <th class="px-3 py-3 font-semibold xl:w-[14%]">Name</th>
                    <th class="px-3 py-3 font-semibold xl:w-[20%]">Email</th>
                    <th class="px-3 py-3 font-semibold xl:w-[12%]">Phone</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Role</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Verified</th>
                    <th class="px-3 py-3 font-semibold xl:w-[12%]">Registered</th>
                    <th class="px-3 py-3 font-semibold text-right xl:w-[16%]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-3 py-3 text-fs-secondary">{{ $user->id }}</td>
                        <td class="px-3 py-3 font-medium text-fs-primary xl:truncate">{{ $user->name }}</td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">{{ $user->email }}</td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">{{ $user->phone ?? '—' }}</td>
                        <td class="px-3 py-3"><x-badge :tone="$user->role === 'admin' ? 'sale' : 'muted'">{{ $user->role }}</x-badge></td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $user->hasVerifiedEmail() ? 'Yes' : 'No' }}</td>
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap justify-end gap-1">
                                <x-button variant="ghost" href="{{ route('admin.users.show', $user) }}" class="!px-3">View</x-button>
                                <x-button variant="secondary" href="{{ route('admin.users.edit', $user) }}" class="!px-3">Edit</x-button>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="ghost" class="!px-3 !text-fs-danger">Delete</x-button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-fs-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($users->hasPages())
    <div class="mt-4">{{ $users->links() }}</div>
@endif
@endsection
