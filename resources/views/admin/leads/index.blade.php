@extends('admin.layout')

@section('title', 'Leads')

@section('content')
<div class="flex flex-wrap justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-fs-primary">Leads</h1>
        <p class="text-sm text-fs-secondary mt-1">Pipeline from inquiries and assignments</p>
    </div>
    <x-button variant="ghost" href="{{ route('admin.leads.export') }}">Export CSV</x-button>
</div>

<form method="POST" action="{{ route('admin.leads.bulk') }}" class="space-y-4">
    @csrf
    <div class="flex flex-wrap gap-2 items-end">
        <x-select label="Bulk" name="action" :options="['status' => 'Set status', 'assign' => 'Assign agent', 'delete' => 'Delete']" :selected="'status'" />
        <x-select label="Status" name="status" :options="collect(App\Models\Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()" :selected="'contacted'" />
        <x-select label="Agent" name="assigned_agent_id" :options="$agents->mapWithKeys(fn ($a) => [$a->id => $a->name])->all()" :selected="null" placeholder="—" />
        <x-button type="submit" variant="secondary">Apply</x-button>
    </div>

    <div class="rounded-md bg-white border border-black/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-fs-surface text-xs uppercase text-fs-muted text-left">
                <tr>
                    <th class="px-3 py-3"></th>
                    <th class="px-3 py-3">Name</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Listing</th>
                    <th class="px-3 py-3">Agent</th>
                    <th class="px-3 py-3">Follow-up</th>
                    <th class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($leads as $lead)
                    <tr>
                        <td class="px-3 py-3"><input type="checkbox" name="ids[]" value="{{ $lead->id }}"></td>
                        <td class="px-3 py-3">{{ $lead->name }}<div class="text-xs text-fs-muted">{{ $lead->email }}</div></td>
                        <td class="px-3 py-3 capitalize">{{ $lead->status }}</td>
                        <td class="px-3 py-3">{{ $lead->listing?->title ?? '—' }}</td>
                        <td class="px-3 py-3">{{ $lead->assignedAgent?->name ?? '—' }}</td>
                        <td class="px-3 py-3">{{ optional($lead->follow_up_at)->toDateString() ?? '—' }}</td>
                        <td class="px-3 py-3 text-right"><a href="{{ route('admin.leads.edit', $lead) }}" class="text-fs-brand hover:underline">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-fs-muted">No leads yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>
<div class="mt-4">{{ $leads->links() }}</div>
@endsection
