@extends('include.master')

@section('title', 'Notifications')

@section('content')
<x-container class="py-10 max-w-2xl">
    <x-page-header title="Notifications">
        <x-slot:actions>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <x-button type="submit" variant="ghost">Mark all read</x-button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="space-y-3">
        @forelse($notifications as $notification)
            <x-panel class="text-sm {{ $notification->read_at ? 'opacity-75' : 'ring-1 ring-fs-brand/20' }}">
                <p class="text-fs-primary">{{ $notification->data['message'] ?? class_basename($notification->type) }}</p>
                <p class="text-xs text-fs-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                @unless($notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="mt-3">
                        @csrf
                        <x-button type="submit" variant="ghost" class="!h-9 !px-3 !text-xs">Mark read</x-button>
                    </form>
                @endunless
            </x-panel>
        @empty
            <x-empty-state
                title="No notifications yet"
                description="Updates about bookings, payments, and account activity will appear here."
            />
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</x-container>
@endsection
