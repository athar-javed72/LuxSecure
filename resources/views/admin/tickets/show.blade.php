@extends('admin.layout')

@section('title', 'Ticket #'.$ticket->id)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tickets', 'href' => route('admin.tickets.index')],
    ['label' => '#'.$ticket->id],
]" />

<x-page-header :title="$ticket->subject" :subtitle="'From '.$ticket->user->name.' · '.$ticket->created_at->toDayDateTimeString()" />

<x-panel class="text-sm whitespace-pre-wrap">{{ $ticket->body }}</x-panel>

@can('update', $ticket)
    <x-panel class="mt-4">
        <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @csrf
            @method('PUT')
            <x-select label="Status" name="status" :options="collect(App\Models\Ticket::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()" :selected="$ticket->status" />
            <x-select label="Priority" name="priority" :options="collect(App\Models\Ticket::PRIORITIES)->mapWithKeys(fn ($p) => [$p => ucfirst($p)])->all()" :selected="$ticket->priority" />
            <x-select label="Assignee" name="assigned_to" :options="$supportUsers->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()" :selected="$ticket->assigned_to" placeholder="—" />
            <div class="sm:col-span-3"><x-button type="submit" variant="primary">Update ticket</x-button></div>
        </form>
    </x-panel>
@endcan

<div class="mt-6 space-y-3">
    <h2 class="fs-display text-base font-semibold text-fs-primary">Replies</h2>
    @forelse ($ticket->replies as $reply)
        <x-panel class="text-sm">
            <p class="text-xs text-fs-muted mb-2">{{ $reply->user->name }} · {{ $reply->created_at->diffForHumans() }} @if($reply->is_internal)<span class="text-fs-danger">(internal)</span>@endif</p>
            <p class="whitespace-pre-wrap">{{ $reply->body }}</p>
        </x-panel>
    @empty
        <p class="text-sm text-fs-muted">No replies yet.</p>
    @endforelse
</div>

@can('update', $ticket)
    <x-panel class="mt-4 max-w-2xl">
        <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="space-y-3">
            @csrf
            <label for="reply-body" class="block text-xs font-semibold text-fs-primary">Reply</label>
            <textarea id="reply-body" name="body" rows="4" required class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20" placeholder="Write a reply…"></textarea>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_internal" value="1" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"> Internal note</label>
            <x-button type="submit" variant="primary">Reply</x-button>
        </form>
    </x-panel>
@endcan
@endsection
