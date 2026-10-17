@extends('admin.layout')

@section('title', 'Activity log')

@section('content')
<h1 class="text-xl font-bold mb-4">Activity log</h1>
<form method="GET" class="mb-4 max-w-md">
    <x-input label="Search" name="search" :value="request('search')" />
</form>
<div class="rounded-md bg-white border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-xs uppercase text-fs-muted text-left">
            <tr>
                <th class="px-3 py-3">When</th>
                <th class="px-3 py-3">Who</th>
                <th class="px-3 py-3">Event</th>
                <th class="px-3 py-3">Subject</th>
                <th class="px-3 py-3">Description</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($activities as $activity)
                <tr>
                    <td class="px-3 py-3 whitespace-nowrap">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-3 py-3">{{ $activity->causer?->name ?? 'System' }}</td>
                    <td class="px-3 py-3">{{ $activity->event ?? $activity->description }}</td>
                    <td class="px-3 py-3">{{ class_basename($activity->subject_type ?? '') }} #{{ $activity->subject_id }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ Str::limit($activity->description, 80) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-fs-muted">No activity recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $activities->links() }}</div>
@endsection
