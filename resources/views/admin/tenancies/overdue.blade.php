@extends('admin.layout')

@section('title', 'Overdue rent')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies', 'href' => route('admin.tenancies.index')],
    ['label' => 'Overdue'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Overdue rent</h1>
    <p class="mt-2 text-sm text-fs-secondary">Payments past due date still pending</p>
</div>

<div class="space-y-4">
    @forelse ($tenancies as $tenancy)
        <div class="rounded-md bg-white border border-black/5 p-4">
            <div class="flex flex-wrap justify-between gap-2 mb-3">
                <div>
                    <p class="font-semibold text-fs-primary">{{ $tenancy->listing->title }}</p>
                    <p class="text-sm text-fs-secondary">{{ $tenancy->tenant->name }}</p>
                </div>
                <a href="{{ route('admin.tenancies.show', $tenancy) }}" class="text-sm text-fs-brand hover:underline">Open tenancy</a>
            </div>
            <ul class="text-sm space-y-1">
                @foreach ($tenancy->payments as $payment)
                    <li class="flex justify-between gap-4 border-t border-black/5 py-2">
                        <span>Due {{ $payment->due_date->toDateString() }}</span>
                        <span class="font-medium text-fs-danger">{{ number_format((float) $payment->amount, 0) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <p class="text-fs-muted text-sm">No overdue payments.</p>
    @endforelse
</div>

<div class="mt-4">{{ $tenancies->links() }}</div>
@endsection
