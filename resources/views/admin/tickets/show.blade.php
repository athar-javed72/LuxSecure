@extends('admin.layout')

@section('title', 'Ticket #'.$ticket->id)

@section('content')
<h1 class="text-xl font-bold">{{ $ticket->subject }}</h1>
<p class="text-sm text-fs-secondary mt-1">From {{ $ticket->user->name }} · {{ $ticket->created_at->toDayDateTimeString() }}</p>

<div class="mt-4 rounded-md bg-white border border-black/5 p-4 text-sm whitespace-pre-wrap">{{ $ticket->body }}</div>

@can('update', $ticket)
<form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 rounded-md bg-white border border-black/5 p-4">
    @csrf
    @method('PUT')
    <x-select label="Status" name="status" :options="collect(App\Models\Ticket::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()" :selected="$ticket->status" />
    <x-select label="Priority" name="priority" :options="collect(App\Models\Ticket::PRIORITIES)->mapWithKeys(fn ($p) => [$p => ucfirst($p)])->all()" :selected="$ticket->priority" />
    <x-select label="Assignee" name="assigned_to" :options="$supportUsers->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()" :selected="$ticket->assigned_to" placeholder="—" />
    <div class="sm:col-span-3"><x-button type="submit" variant="primary">Update ticket</x-button></div>
</form>
@endcan

<div class="mt-6 space-y-3">
    <h2 class="font-semibold">Replies</h2>
    @foreach ($ticket->replies as $reply)
        <div class="rounded-md border border-black/5 bg-white p-4 text-sm">
            <p class="text-xs text-fs-muted mb-2">{{ $reply->user->name }} · {{ $reply->created_at->diffForHumans() }} @if($reply->is_internal)<span class="text-fs-danger">(internal)</span>@endif</p>
            <p class="whitespace-pre-wrap">{{ $reply->body }}</p>
        </div>
    @endforeach
</div>

@can('update', $ticket)
<form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="mt-4 space-y-3 max-w-2xl">
    @csrf
    <textarea name="body" rows="4" required class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm" placeholder="Write a reply…"></textarea>
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_internal" value="1"> Internal note</label>
    <x-button type="submit" variant="primary">Reply</x-button>
</form>
@endcan
@endsection
