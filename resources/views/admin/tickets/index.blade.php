@extends('admin.layout')

@section('title', 'Tickets')

@section('content')
<div class="flex flex-wrap justify-between gap-4 mb-6">
    <h1 class="text-xl font-bold text-fs-primary">Support tickets</h1>
    @can('create', App\Models\Ticket::class)
        <x-button variant="primary" href="{{ route('admin.tickets.create') }}">New ticket</x-button>
    @endcan
</div>
<div class="rounded-md bg-white border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-xs uppercase text-fs-muted text-left">
            <tr>
                <th class="px-3 py-3">Subject</th>
                <th class="px-3 py-3">Requester</th>
                <th class="px-3 py-3">Priority</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Assignee</th>
                <th class="px-3 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($tickets as $ticket)
                <tr>
                    <td class="px-3 py-3 font-medium">{{ $ticket->subject }}</td>
                    <td class="px-3 py-3">{{ $ticket->user->name }}</td>
                    <td class="px-3 py-3 capitalize">{{ $ticket->priority }}</td>
                    <td class="px-3 py-3 capitalize">{{ $ticket->status }}</td>
                    <td class="px-3 py-3">{{ $ticket->assignee?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-right"><a class="text-fs-brand hover:underline" href="{{ route('admin.tickets.show', $ticket) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No tickets.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $tickets->links() }}</div>
@endsection
