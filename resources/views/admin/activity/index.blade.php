@extends('admin.layout')

@section('title', 'Activity log')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Activity log'],
]" />

<x-page-header title="Activity log" />

<x-panel class="mb-4 max-w-md">
    <form method="GET">
        <x-input label="Search" name="search" :value="request('search')" />
        <div class="mt-3"><x-button type="submit" variant="primary">Search</x-button></div>
    </form>
</x-panel>

@if($activities->isEmpty())
    <x-empty-state title="No activity recorded yet" description="Audit events will appear as users work in the admin." />
@else
    <x-table caption="Activity log" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>When</th>
                    <th>Who</th>
                    <th>Event</th>
                    <th>Subject</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $activity)
                    <tr>
                        <td data-label="When" class="whitespace-nowrap">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        <td data-label="Who">{{ $activity->causer?->name ?? 'System' }}</td>
                        <td data-label="Event">{{ $activity->event ?? $activity->description }}</td>
                        <td data-label="Subject">{{ class_basename($activity->subject_type ?? '') }} #{{ $activity->subject_id }}</td>
                        <td data-label="Description" class="text-fs-secondary">{{ Str::limit($activity->description, 80) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $activities->links() }}</div>
@endsection
