@extends('admin.layout')

@section('title', 'Reviews')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Reviews'],
]" />

<x-page-header title="Reviews" />

<x-panel class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <x-select label="Status" name="status" :options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']" :selected="request('status')" placeholder="All" />
        <x-button type="submit" variant="primary">Filter</x-button>
    </form>
</x-panel>

@if($reviews->isEmpty())
    <x-empty-state title="No reviews" description="Listing reviews awaiting moderation will appear here." />
@else
    <x-table caption="Reviews" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Listing</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Body</th>
                    <th class="text-right">Moderate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                    <tr class="align-top">
                        <td data-label="Listing">{{ $review->listing?->title }}</td>
                        <td data-label="User">{{ $review->user?->name }}</td>
                        <td data-label="Rating">{{ $review->rating }}/5</td>
                        <td data-label="Status"><x-status-badge :status="$review->status" /></td>
                        <td data-label="Body" class="max-w-xs">{{ Str::limit($review->body, 80) }}</td>
                        <td data-label="Moderate" class="text-right space-x-2">
                            @if($review->status !== 'approved')
                                <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <x-button type="submit" variant="ghost" class="!px-2">Approve</x-button>
                                </form>
                            @endif
                            @if($review->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <x-button type="submit" variant="ghost" class="!px-2 !text-fs-danger">Reject</x-button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
