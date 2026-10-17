@extends('admin.layout')

@section('title', 'Lead')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Leads', 'href' => route('admin.leads.index')],
    ['label' => $lead->name],
]" />

<x-page-header :title="'Lead: '.$lead->name" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <x-select label="Status" name="status" :options="collect(App\Models\Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()" :selected="old('status', $lead->status)" />
        <x-select label="Assigned agent" name="assigned_agent_id" :options="$agents->mapWithKeys(fn ($a) => [$a->id => $a->name])->all()" :selected="old('assigned_agent_id', $lead->assigned_agent_id)" placeholder="Unassigned" />
        <x-input label="Phone" name="phone" :value="old('phone', $lead->phone)" />
        <x-input label="Follow up at" name="follow_up_at" type="datetime-local" :value="old('follow_up_at', optional($lead->follow_up_at)->format('Y-m-d\TH:i'))" />
        <div>
            <label for="notes" class="block text-xs font-semibold text-fs-primary mb-1">Notes</label>
            <textarea id="notes" name="notes" rows="5" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('notes', $lead->notes) }}</textarea>
        </div>
        <p class="text-sm text-fs-secondary">Listing: {{ $lead->listing?->title ?? '—' }}</p>
        <x-button type="submit" variant="primary">Save</x-button>
    </form>
</x-panel>
@endsection
