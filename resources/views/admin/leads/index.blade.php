@extends('admin.layout')

@section('title', 'Leads')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Leads'],
]" />

<x-page-header title="Leads" subtitle="Pipeline from inquiries and assignments">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.leads.export') }}">Export CSV</x-button>
    </x-slot:actions>
</x-page-header>

<form method="POST" action="{{ route('admin.leads.bulk') }}" class="space-y-4">
    @csrf
    <x-panel>
        <div class="flex flex-wrap gap-2 items-end">
            <x-select label="Bulk" name="action" :options="['status' => 'Set status', 'assign' => 'Assign agent', 'delete' => 'Delete']" :selected="'status'" />
            <x-select label="Status" name="status" :options="collect(App\Models\Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()" :selected="'contacted'" />
            <x-select label="Agent" name="assigned_agent_id" :options="$agents->mapWithKeys(fn ($a) => [$a->id => $a->name])->all()" :selected="null" placeholder="—" />
            <x-button type="submit" variant="secondary">Apply</x-button>
        </div>
    </x-panel>

    @if($leads->isEmpty())
        <x-empty-state title="No leads yet" description="Inquiries and assignments will populate your pipeline." />
    @else
        <x-table caption="Leads" class="fs-table-stack">
                <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                    <tr>
                        <th><span class="sr-only">Select</span></th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Listing</th>
                        <th>Agent</th>
                        <th>Follow-up</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leads as $lead)
                        <tr>
                            <td data-label="Select"><input type="checkbox" name="ids[]" value="{{ $lead->id }}" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"></td>
                            <td data-label="Name">{{ $lead->name }}<div class="text-xs text-fs-muted">{{ $lead->email }}</div></td>
                            <td data-label="Status"><x-status-badge :status="$lead->status" /></td>
                            <td data-label="Listing">{{ $lead->listing?->title ?? '—' }}</td>
                            <td data-label="Agent">{{ $lead->assignedAgent?->name ?? '—' }}</td>
                            <td data-label="Follow-up">{{ optional($lead->follow_up_at)->toDateString() ?? '—' }}</td>
                            <td data-label="Actions" class="text-right">
                                <x-button variant="ghost" href="{{ route('admin.leads.edit', $lead) }}" class="!px-3">Edit</x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
    @endif
</form>
<div class="mt-4">{{ $leads->links() }}</div>
@endsection
