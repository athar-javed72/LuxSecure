@extends('admin.layout')

@section('title', 'KYC')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'KYC'],
]" />

<h1 class="fs-display text-2xl text-fs-primary mb-6">KYC documents</h1>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <x-select label="Status" name="status" :options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']" :selected="request('status')" placeholder="All" />
    <div class="flex items-end"><x-button type="submit" variant="primary">Filter</x-button></div>
</form>

<div class="overflow-x-auto rounded-md bg-white border border-black/5">
    <table class="min-w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3">User</th>
                <th class="px-3 py-3">Type</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">File</th>
                <th class="px-3 py-3 text-right">Review</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
                <tr class="border-t border-black/5 align-top">
                    <td class="px-3 py-3">{{ $doc->user?->name }}<br><span class="text-xs text-fs-muted">{{ $doc->user?->email }}</span></td>
                    <td class="px-3 py-3 uppercase">{{ $doc->type }}</td>
                    <td class="px-3 py-3">{{ $doc->status }}</td>
                    <td class="px-3 py-3"><a class="text-fs-brand hover:underline" href="{{ route('admin.kyc.download', $doc) }}">Download</a></td>
                    <td class="px-3 py-3 text-right">
                        @if($doc->status === 'pending')
                            <form method="POST" action="{{ route('admin.kyc.review', $doc) }}" class="inline-flex flex-col gap-2 items-end">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="reason" placeholder="Reason (if reject)" class="h-9 px-2 rounded-sm border border-black/10 text-xs w-48">
                                <div class="space-x-2">
                                    <button type="submit" name="status" value="approved" class="text-fs-brand hover:underline">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="text-fs-danger hover:underline">Reject</button>
                                </div>
                            </form>
                        @else
                            <span class="text-xs text-fs-muted">{{ optional($doc->reviewed_at)->toDateTimeString() }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-3 py-8 text-center text-fs-muted">No KYC documents.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $documents->links() }}</div>
@endsection
