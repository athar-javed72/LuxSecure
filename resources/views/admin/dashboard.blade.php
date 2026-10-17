@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Dashboard'],
]" />

<x-page-header title="Dashboard" :subtitle="'Overview of your '.($siteName ?? config('app.name')).' admin panel'" />

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-stat-card label="Total users" :value="$totalUsers" :href="route('admin.users.index')" />
    <x-stat-card label="Listings" :value="$totalProperties" :href="route('admin.listings.index')" />
    <x-stat-card label="Inquiries" :value="$totalInquiries" :href="route('admin.inquiries.index')" />
    <x-stat-card label="Unread" :value="$unreadInquiries" hint="Unread inquiries" />
</div>

<div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-6">
    <x-panel :padding="false">
        <div class="px-4 py-3 border-b border-black/5 flex items-center justify-between gap-2">
            <h2 class="fs-display text-base font-semibold text-fs-primary">Recent listings</h2>
            <x-button variant="ghost" href="{{ route('admin.listings.index') }}">View all</x-button>
        </div>
        @if($recentProperties->isEmpty())
            <x-empty-state title="No listings yet" description="Published listings will appear here." action-label="Manage listings" :action-href="route('admin.listings.index')" class="border-0 shadow-none rounded-none" />
        @else
            <x-table caption="Recent listings" class="fs-table-stack">
                    <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProperties as $p)
                            <tr>
                                <td data-label="Title" class="font-medium text-fs-primary truncate">{{ $p->title }}</td>
                                <td data-label="Type" class="text-fs-secondary truncate">{{ $p->listingType?->name }}</td>
                                <td data-label="Price" class="text-fs-secondary whitespace-nowrap">{{ $p->formatted_price }}</td>
                                <td data-label="Status"><x-status-badge :status="$p->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
        @endif
    </x-panel>

    <x-panel :padding="false">
        <div class="px-4 py-3 border-b border-black/5 flex items-center justify-between gap-2">
            <h2 class="fs-display text-base font-semibold text-fs-primary">Recent inquiries</h2>
            <x-button variant="ghost" href="{{ route('admin.inquiries.index') }}">View all</x-button>
        </div>
        @if($recentInquiries->isEmpty())
            <x-empty-state title="No inquiries yet" description="New contact requests will show up here." action-label="View inquiries" :action-href="route('admin.inquiries.index')" class="border-0 shadow-none rounded-none" />
        @else
            <x-table caption="Recent inquiries" class="fs-table-stack">
                    <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Read</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentInquiries as $inquiry)
                            <tr>
                                <td data-label="Name" class="font-medium text-fs-primary truncate">{{ $inquiry->name }}</td>
                                <td data-label="Email" class="text-fs-secondary truncate">{{ $inquiry->email }}</td>
                                <td data-label="Date" class="text-fs-secondary whitespace-nowrap">{{ $inquiry->created_at->format('M j') }}</td>
                                <td data-label="Read">
                                    <x-status-badge :status="$inquiry->is_read ? 'closed' : 'new'">{{ $inquiry->is_read ? 'Read' : 'New' }}</x-status-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
        @endif
    </x-panel>
</div>
@endsection
