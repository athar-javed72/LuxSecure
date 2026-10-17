@extends('include.master')

@section('title', 'My tenancy')

@section('content')
<x-container class="py-8">
    <h1 class="text-2xl font-bold text-fs-primary">My tenancy</h1>
    <p class="mt-2 text-sm text-fs-secondary">Active leases and rent payment history</p>

    <div class="mt-6 space-y-4">
        @forelse ($tenancies as $tenancy)
            <article class="rounded-md bg-white border border-black/5 p-5 flex flex-wrap justify-between gap-4">
                <div>
                    <h2 class="font-semibold">{{ $tenancy->listing->title }}</h2>
                    <p class="text-sm text-fs-secondary mt-1">
                        {{ $tenancy->start_date->toDateString() }} → {{ optional($tenancy->end_date)->toDateString() ?? 'open' }}
                        · {{ number_format((float) $tenancy->monthly_rent, 0) }}/mo
                    </p>
                    <p class="text-sm mt-1 capitalize">Status: {{ $tenancy->status }}</p>
                </div>
                <x-button variant="ghost" href="{{ route('tenancies.show', $tenancy) }}">Payment history</x-button>
            </article>
        @empty
            <p class="text-fs-muted text-sm">No tenancies assigned to your account.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $tenancies->links() }}</div>
</x-container>
@endsection
