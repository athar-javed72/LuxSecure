@extends('admin.layout')

@section('title', 'Tickets')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tickets'],
]" />

<x-page-header title="Support tickets">
    <x-slot:actions>
        @can('create', App\Models\Ticket::class)
            <x-button variant="primary" href="{{ route('admin.tickets.create') }}">New ticket</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

@if($tickets->isEmpty())
    <x-empty-state
        title="No tickets"
        description="Support requests from users will appear here."
        action-label="New ticket"
        :action-href="route('admin.tickets.create')"
    />
@else
    <x-table caption="Support tickets" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Subject</th>
                    <th>Requester</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assignee</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td data-label="Subject" class="font-medium">{{ $ticket->subject }}</td>
                        <td data-label="Requester">{{ $ticket->user->name }}</td>
                        <td data-label="Priority" class="capitalize">{{ $ticket->priority }}</td>
                        <td data-label="Status"><x-status-badge :status="$ticket->status" /></td>
                        <td data-label="Assignee">{{ $ticket->assignee?->name ?? '—' }}</td>
                        <td data-label="Actions" class="text-right">
                            <x-button variant="ghost" href="{{ route('admin.tickets.show', $ticket) }}" class="!px-3">Open</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $tickets->links() }}</div>
@endsection
