@extends('admin.layout')

@section('title', 'Lead')

@section('content')
<h1 class="text-xl font-bold text-fs-primary mb-4">Lead: {{ $lead->name }}</h1>
<form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="max-w-xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    @csrf
    @method('PUT')
    <x-select label="Status" name="status" :options="collect(App\Models\Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()" :selected="old('status', $lead->status)" />
    <x-select label="Assigned agent" name="assigned_agent_id" :options="$agents->mapWithKeys(fn ($a) => [$a->id => $a->name])->all()" :selected="old('assigned_agent_id', $lead->assigned_agent_id)" placeholder="Unassigned" />
    <x-input label="Phone" name="phone" :value="old('phone', $lead->phone)" />
    <x-input label="Follow up at" name="follow_up_at" type="datetime-local" :value="old('follow_up_at', optional($lead->follow_up_at)->format('Y-m-d\TH:i'))" />
    <div>
        <label class="block text-xs font-semibold mb-1">Notes</label>
        <textarea name="notes" rows="5" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ old('notes', $lead->notes) }}</textarea>
    </div>
    <p class="text-sm text-fs-secondary">Listing: {{ $lead->listing?->title ?? '—' }}</p>
    <x-button type="submit" variant="primary">Save</x-button>
</form>
@endsection
