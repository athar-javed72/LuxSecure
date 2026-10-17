@extends('include.master')

@section('title', 'Notifications')

@section('content')
<x-container class="py-10 max-w-2xl">
    <div class="flex items-center justify-between gap-3">
        <h1 class="fs-display text-3xl text-fs-primary">Notifications</h1>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <x-button type="submit" variant="ghost">Mark all read</x-button>
        </form>
    </div>

    <div class="mt-6 space-y-3">
        @forelse($notifications as $notification)
            <div class="rounded-md border border-black/5 bg-white p-4 text-sm {{ $notification->read_at ? 'opacity-70' : '' }}">
                <p>{{ $notification->data['message'] ?? class_basename($notification->type) }}</p>
                <p class="text-xs text-fs-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                @unless($notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-fs-brand text-xs hover:underline">Mark read</button>
                    </form>
                @endunless
            </div>
        @empty
            <p class="text-sm text-fs-muted">No notifications yet.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</x-container>
@endsection
