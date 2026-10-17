@extends('include.master')

@section('title', 'My tenancy')

@section('content')
<x-container class="py-8">
    <x-page-header
        title="My tenancy"
        subtitle="Active leases and rent payment history"
    />

    <div class="space-y-4">
        @forelse ($tenancies as $tenancy)
            <x-panel class="flex flex-wrap justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="fs-display text-lg text-fs-primary">{{ $tenancy->listing->title }}</h2>
                        <x-status-badge :status="$tenancy->status" />
                    </div>
                    <p class="text-sm text-fs-secondary mt-1">
                        {{ $tenancy->start_date->toDateString() }} → {{ optional($tenancy->end_date)->toDateString() ?? 'open' }}
                        · {{ number_format((float) $tenancy->monthly_rent, 0) }}/mo
                    </p>
                </div>
                <x-button variant="ghost" href="{{ route('tenancies.show', $tenancy) }}">Payment history</x-button>
            </x-panel>
        @empty
            <x-empty-state
                title="No tenancies yet"
                description="When a lease is linked to your account, rent schedules and receipts will show here."
                action-label="Browse properties"
                :action-href="route('properties')"
            />
        @endforelse
    </div>

    <div class="mt-6">{{ $tenancies->links() }}</div>
</x-container>
@endsection
