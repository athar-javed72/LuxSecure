@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
@php
    $recentProperties = \App\Models\Property::latest()->take(5)->get();
    $recentInquiries = \App\Models\ContactInquiry::latest()->take(5)->get();
@endphp

<div class="mb-8">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Dashboard</h1>
    <p class="mt-2 text-sm text-fs-secondary max-w-prose">Overview of your LuxSecure admin panel</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('admin.users.index') }}" class="rounded-md bg-white shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-shadow duration-150">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Total users</h2>
        <p class="mt-2 text-xl font-bold text-fs-primary">{{ $totalUsers }}</p>
    </a>
    <a href="{{ route('admin.properties.index') }}" class="rounded-md bg-white shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-shadow duration-150">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Properties</h2>
        <p class="mt-2 text-xl font-bold text-fs-primary">{{ $totalProperties }}</p>
    </a>
    <a href="{{ route('admin.inquiries.index') }}" class="rounded-md bg-white shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-shadow duration-150">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Inquiries</h2>
        <p class="mt-2 text-xl font-bold text-fs-primary">{{ $totalInquiries }}</p>
    </a>
    <div class="rounded-md bg-white shadow-sm border border-slate-100 p-6">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Unread</h2>
        <p class="mt-2 text-xl font-bold text-fs-primary">{{ $unreadInquiries }}</p>
    </div>
</div>

<div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="rounded-md bg-white shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
            <h2 class="text-base font-semibold text-fs-primary">Recent properties</h2>
            <x-button variant="ghost" href="{{ route('admin.properties.index') }}">View all</x-button>
        </div>
        <div class="overflow-x-auto xl:overflow-x-visible">
            <table class="w-full text-sm table-fixed">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-fs-muted">
                    <tr>
                        <th class="px-4 py-3 font-semibold w-[40%]">Title</th>
                        <th class="px-4 py-3 font-semibold w-[25%]">Type</th>
                        <th class="px-4 py-3 font-semibold w-[20%]">Price</th>
                        <th class="px-4 py-3 font-semibold w-[15%]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentProperties as $p)
                        <tr>
                            <td class="px-4 py-3 font-medium text-fs-primary truncate">{{ $p->title }}</td>
                            <td class="px-4 py-3 text-fs-secondary truncate">{{ $p->type }}</td>
                            <td class="px-4 py-3 text-fs-secondary whitespace-nowrap">PKR {{ number_format($p->price / 1_00_00_000, 1) }} Cr</td>
                            <td class="px-4 py-3"><x-badge :tone="$p->is_active ? 'success' : 'muted'">{{ $p->is_active ? 'Active' : 'Off' }}</x-badge></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-fs-muted">No properties yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-md bg-white shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
            <h2 class="text-base font-semibold text-fs-primary">Recent inquiries</h2>
            <x-button variant="ghost" href="{{ route('admin.inquiries.index') }}">View all</x-button>
        </div>
        <div class="overflow-x-auto xl:overflow-x-visible">
            <table class="w-full text-sm table-fixed">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-fs-muted">
                    <tr>
                        <th class="px-4 py-3 font-semibold w-[30%]">Name</th>
                        <th class="px-4 py-3 font-semibold w-[40%]">Email</th>
                        <th class="px-4 py-3 font-semibold w-[15%]">Date</th>
                        <th class="px-4 py-3 font-semibold w-[15%]">Read</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentInquiries as $inquiry)
                        <tr>
                            <td class="px-4 py-3 font-medium text-fs-primary truncate">{{ $inquiry->name }}</td>
                            <td class="px-4 py-3 text-fs-secondary truncate">{{ $inquiry->email }}</td>
                            <td class="px-4 py-3 text-fs-secondary whitespace-nowrap">{{ $inquiry->created_at->format('M j') }}</td>
                            <td class="px-4 py-3"><x-badge :tone="$inquiry->is_read ? 'muted' : 'success'">{{ $inquiry->is_read ? 'Read' : 'New' }}</x-badge></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-fs-muted">No inquiries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
