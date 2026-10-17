@extends('admin.layout')

@section('title', 'Inquiries')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Inquiries'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Inquiries</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Contact form submissions</p>
    </div>
    <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
</div>

<form method="GET" action="{{ route('admin.inquiries.index') }}" class="mb-6 rounded-md bg-white shadow-sm border border-black/5 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 items-end w-full max-w-5xl">
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

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold xl:w-[12%]">Date</th>
                    <th class="px-3 py-3 font-semibold xl:w-[12%]">Name</th>
                    <th class="px-3 py-3 font-semibold xl:w-[16%]">Email</th>
                    <th class="px-3 py-3 font-semibold xl:w-[16%]">Property</th>
                    <th class="px-3 py-3 font-semibold xl:w-[24%]">Message</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Read</th>
                    <th class="px-3 py-3 font-semibold text-right xl:w-[10%]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($inquiries as $inquiry)
                    <tr class="{{ $inquiry->is_read ? '' : 'bg-fs-brand-light/40' }}">
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">{{ $inquiry->created_at->format('M j, Y H:i') }}</td>
                        <td class="px-3 py-3 font-medium text-fs-primary xl:truncate">{{ $inquiry->name }}</td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate"><a href="mailto:{{ $inquiry->email }}" class="text-fs-brand">{{ $inquiry->email }}</a></td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">
                            @if($inquiry->listing)
                                <a href="{{ route('admin.listings.show', $inquiry->listing) }}" class="text-fs-brand hover:underline">{{ $inquiry->listing->title }}</a>
                            @else
                                <span class="text-fs-muted">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">{{ Str::limit($inquiry->message, 80) }}</td>
                        <td class="px-3 py-3">
                            <x-badge :tone="$inquiry->is_read ? 'muted' : 'success'">{{ $inquiry->is_read ? 'Read' : 'New' }}</x-badge>
                        </td>
                        <td class="px-3 py-3 text-right">
                            @if(!$inquiry->is_read)
                                <form action="{{ route('admin.inquiries.markRead', $inquiry) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-button type="submit" variant="secondary" class="!px-3">Mark read</x-button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-fs-muted">No inquiries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($inquiries->hasPages())
    <div class="mt-4">{{ $inquiries->links() }}</div>
@endif
@endsection
