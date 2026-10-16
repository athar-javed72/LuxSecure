@extends('admin.layout')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">User details</h1>
    <x-button variant="ghost" href="{{ route('admin.users.index') }}">Back</x-button>
</div>

<div class="rounded-md bg-white shadow-sm border border-slate-100 p-6 w-full max-w-5xl">
    <h2 class="text-lg font-bold text-fs-primary mb-6">{{ $user->name }}</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-xs text-fs-muted">ID</dt><dd class="mt-1">{{ $user->id }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Email</dt><dd class="mt-1">{{ $user->email }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Phone</dt><dd class="mt-1">{{ $user->phone ?? '—' }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Role</dt><dd class="mt-1"><x-badge :tone="$user->role === 'admin' ? 'sale' : 'muted'">{{ $user->role }}</x-badge></dd></div>
        <div><dt class="text-xs text-fs-muted">Verified</dt><dd class="mt-1">{{ $user->hasVerifiedEmail() ? 'Yes' : 'No' }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Registered</dt><dd class="mt-1">{{ $user->created_at->format('M j, Y H:i') }}</dd></div>
    </dl>
    <div class="mt-6 flex flex-wrap gap-2">
        <x-button variant="primary" href="{{ route('admin.users.edit', $user) }}">Edit</x-button>
        @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="ghost" class="!text-fs-danger">Delete</x-button>
            </form>
        @endif
    </div>
</div>
@endsection
