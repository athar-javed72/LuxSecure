@extends('admin.layout')

@section('title', 'Inquiries')

@section('content')
<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Inquiries</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Contact form submissions</p>
    </div>
    <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
</div>

<div class="rounded-md bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto xl:overflow-x-visible">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold xl:w-[14%]">Date</th>
                    <th class="px-3 py-3 font-semibold xl:w-[14%]">Name</th>
                    <th class="px-3 py-3 font-semibold xl:w-[18%]">Email</th>
                    <th class="px-3 py-3 font-semibold xl:w-[34%]">Message</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Read</th>
                    <th class="px-3 py-3 font-semibold text-right xl:w-[10%]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($inquiries as $inquiry)
                    <tr class="{{ $inquiry->is_read ? '' : 'bg-fs-brand-light/40' }}">
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">{{ $inquiry->created_at->format('M j, Y H:i') }}</td>
                        <td class="px-3 py-3 font-medium text-fs-primary xl:truncate">{{ $inquiry->name }}</td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate"><a href="mailto:{{ $inquiry->email }}" class="text-fs-brand">{{ $inquiry->email }}</a></td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">{{ Str::limit($inquiry->message, 80) }}</td>
                        <td class="px-3 py-3">
                            <x-badge :tone="$inquiry->is_read ? 'muted' : 'success'">{{ $inquiry->is_read ? 'Read' : 'New' }}</x-badge>
                        </td>
                        <td class="px-3 py-3 text-right">
                            @if(!$inquiry->is_read)
                                <form action="{{ route('admin.inquiries.markRead', $inquiry) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-button type="submit" variant="secondary" class="!px-3">Mark read</x-button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No inquiries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($inquiries->hasPages())
    <div class="mt-4">{{ $inquiries->links() }}</div>
@endif
@endsection
