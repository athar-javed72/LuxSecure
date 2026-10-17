@extends('admin.layout')

@section('title', 'KYC')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'KYC'],
]" />

<x-page-header title="KYC documents" />

<x-panel class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <x-select label="Status" name="status" :options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']" :selected="request('status')" placeholder="All" />
        <x-button type="submit" variant="primary">Filter</x-button>
    </form>
</x-panel>

@if($documents->isEmpty())
    <x-empty-state title="No KYC documents" description="User identity uploads will appear here for review." />
@else
    <x-table caption="KYC documents" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>User</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>File</th>
                    <th class="text-right">Review</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                    <tr class="align-top">
                        <td data-label="User">{{ $doc->user?->name }}<br><span class="text-xs text-fs-muted">{{ $doc->user?->email }}</span></td>
                        <td data-label="Type" class="uppercase">{{ $doc->type }}</td>
                        <td data-label="Status"><x-status-badge :status="$doc->status" /></td>
                        <td data-label="File"><x-button variant="ghost" href="{{ route('admin.kyc.download', $doc) }}" class="!px-2">Download</x-button></td>
                        <td data-label="Review" class="text-right">
                            @if($doc->status === 'pending')
                                <form method="POST" action="{{ route('admin.kyc.review', $doc) }}" class="inline-flex flex-col gap-2 items-end">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="reason" placeholder="Reason (if reject)" class="h-9 px-2 rounded-sm border border-black/10 text-xs w-48 focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">
                                    <div class="flex flex-wrap gap-2 justify-end">
                                        <x-button type="submit" name="status" value="approved" variant="ghost" class="!px-2">Approve</x-button>
                                        <x-button type="submit" name="status" value="rejected" variant="ghost" class="!px-2 !text-fs-danger">Reject</x-button>
                                    </div>
                                </form>
                            @else
                                <span class="text-xs text-fs-muted">{{ optional($doc->reviewed_at)->toDateTimeString() }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $documents->links() }}</div>
@endsection
