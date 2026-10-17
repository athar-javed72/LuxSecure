@extends('admin.layout')

@section('title', 'Agents')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Agents'],
]" />

<x-page-header title="Agent profiles">
    <x-slot:actions>
        @can('create', App\Models\AgentProfile::class)
            <x-button variant="primary" href="{{ route('admin.agents.create') }}">Add profile</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

@if($profiles->isEmpty())
    <x-empty-state title="No agent profiles" action-label="Add profile" :action-href="route('admin.agents.create')" />
@else
    <x-table caption="Agent profiles" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Name</th>
                    <th>Agency</th>
                    <th>Phone</th>
                    <th>Verified</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profiles as $profile)
                    <tr>
                        <td data-label="Name" class="font-medium">{{ $profile->user->name }}</td>
                        <td data-label="Agency">{{ $profile->agency ?? '—' }}</td>
                        <td data-label="Phone">{{ $profile->phone ?? '—' }}</td>
                        <td data-label="Verified">
                            <x-status-badge :status="$profile->is_verified ? 'verified' : 'inactive'">{{ $profile->is_verified ? 'Yes' : 'No' }}</x-status-badge>
                        </td>
                        <td data-label="Actions" class="text-right">
                            <x-button variant="ghost" href="{{ route('admin.agents.edit', $profile) }}" class="!px-3">Edit</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $profiles->links() }}</div>
@endsection
