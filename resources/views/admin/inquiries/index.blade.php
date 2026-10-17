@extends('admin.layout')

@section('title', 'Inquiries')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Inquiries'],
]" />

<x-page-header title="Inquiries" subtitle="Contact form submissions">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
    </x-slot:actions>
</x-page-header>

<x-panel class="mb-6 max-w-5xl">
    <form method="GET" action="{{ route('admin.inquiries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 items-end">
        <x-input label="Search" name="search" :value="request('search')" placeholder="Name, email, or message" />
        <x-select
            label="Status"
            name="read"
            :options="['unread' => 'Unread', 'read' => 'Read']"
            :selected="request('read')"
            placeholder="All"
        />
        <div class="flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Filter</x-button>
            @if(request()->hasAny(['search', 'read']))
                <x-button variant="ghost" href="{{ route('admin.inquiries.index') }}">Clear</x-button>
            @endif
        </div>
    </form>
</x-panel>

@if($inquiries->isEmpty())
    <x-empty-state
        title="No inquiries found"
        description="Contact form submissions will appear here."
    />
@else
    <x-table caption="Inquiries" class="fs-table-stack">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Property</th>
                <th scope="col">Message</th>
                <th scope="col">Read</th>
                <th scope="col" class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $inquiry)
                <tr class="{{ $inquiry->is_read ? '' : 'bg-fs-brand-light/40' }}">
                    <td data-label="Date" class="text-fs-secondary whitespace-nowrap">{{ $inquiry->created_at->format('M j, Y H:i') }}</td>
                    <td data-label="Name" class="font-medium text-fs-primary">{{ $inquiry->name }}</td>
                    <td data-label="Email" class="text-fs-secondary">
                        <a href="mailto:{{ $inquiry->email }}" class="text-fs-brand hover:underline">{{ $inquiry->email }}</a>
                    </td>
                    <td data-label="Property" class="text-fs-secondary">
                        @if($inquiry->listing)
                            <a href="{{ route('admin.listings.show', $inquiry->listing) }}" class="text-fs-brand hover:underline">{{ $inquiry->listing->title }}</a>
                        @else
                            <span class="text-fs-muted">—</span>
                        @endif
                    </td>
                    <td data-label="Message" class="text-fs-secondary">{{ Str::limit($inquiry->message, 80) }}</td>
                    <td data-label="Read">
                        <x-badge :tone="$inquiry->is_read ? 'muted' : 'success'">{{ $inquiry->is_read ? 'Read' : 'New' }}</x-badge>
                    </td>
                    <td data-label="Actions">
                        <div class="flex flex-wrap justify-end gap-1">
                            @can('create', App\Models\Lead::class)
                                <form action="{{ route('admin.leads.convert', $inquiry) }}" method="POST" class="inline">
                                    @csrf
                                    <x-button type="submit" variant="ghost" class="!px-3">To lead</x-button>
                                </form>
                            @endcan
                            @if(! $inquiry->is_read)
                                <form action="{{ route('admin.inquiries.markRead', $inquiry) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-button type="submit" variant="secondary" class="!px-3">Mark read</x-button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-table>
@endif

@if($inquiries->hasPages())
    <div class="mt-4">{{ $inquiries->links() }}</div>
@endif
@endsection
