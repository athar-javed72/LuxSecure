@extends('admin.layout')

@section('title', 'Reviews')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Reviews'],
]" />

<h1 class="fs-display text-2xl text-fs-primary mb-6">Reviews</h1>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <x-select label="Status" name="status" :options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']" :selected="request('status')" placeholder="All" />
    <div class="flex items-end"><x-button type="submit" variant="primary">Filter</x-button></div>
</form>

<div class="overflow-x-auto rounded-md bg-white border border-black/5">
    <table class="min-w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3">Listing</th>
                <th class="px-3 py-3">User</th>
                <th class="px-3 py-3">Rating</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Body</th>
                <th class="px-3 py-3 text-right">Moderate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr class="border-t border-black/5 align-top">
                    <td class="px-3 py-3">{{ $review->listing?->title }}</td>
                    <td class="px-3 py-3">{{ $review->user?->name }}</td>
                    <td class="px-3 py-3">{{ $review->rating }}/5</td>
                    <td class="px-3 py-3">{{ $review->status }}</td>
                    <td class="px-3 py-3 max-w-xs">{{ Str::limit($review->body, 80) }}</td>
                    <td class="px-3 py-3 text-right space-x-2">
                        @if($review->status !== 'approved')
                            <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="text-fs-brand hover:underline">Approve</button>
                            </form>
                        @endif
                        @if($review->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="text-fs-danger hover:underline">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-3 py-8 text-center text-fs-muted">No reviews.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
