@extends('admin.layout')

@section('title', 'Overdue rent')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies', 'href' => route('admin.tenancies.index')],
    ['label' => 'Overdue'],
]" />

<x-page-header title="Overdue rent" subtitle="Payments past due date still pending" />

@if($tenancies->isEmpty())
    <x-empty-state title="No overdue payments" description="All rent payments are current." action-label="All tenancies" :action-href="route('admin.tenancies.index')" />
@else
    <div class="space-y-4">
        @foreach ($tenancies as $tenancy)
            <x-panel>
                <div class="flex flex-wrap justify-between gap-2 mb-3">
                    <div>
                        <p class="font-semibold text-fs-primary">{{ $tenancy->listing->title }}</p>
                        <p class="text-sm text-fs-secondary">{{ $tenancy->tenant->name }}</p>
                    </div>
                    <x-button variant="ghost" href="{{ route('admin.tenancies.show', $tenancy) }}" class="!px-3">Open tenancy</x-button>
                </div>
                <ul class="text-sm space-y-1">
                    @foreach ($tenancy->payments as $payment)
                        <li class="flex justify-between gap-4 border-t border-black/5 py-2">
                            <span>Due {{ $payment->due_date->toDateString() }}</span>
                            <span class="font-medium text-fs-danger">{{ number_format((float) $payment->amount, 0) }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-panel>
        @endforeach
    </div>
@endif

<div class="mt-4">{{ $tenancies->links() }}</div>
@endsection
