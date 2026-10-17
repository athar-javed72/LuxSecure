@extends('admin.layout')

@section('title', 'New ticket')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tickets', 'href' => route('admin.tickets.index')],
    ['label' => 'Create'],
]" />

<x-page-header title="New support ticket" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.tickets.store') }}" class="space-y-4">
        @csrf
        <x-input label="Subject" name="subject" :value="old('subject')" required />
        <div>
            <label for="body" class="block text-xs font-semibold text-fs-primary mb-1">Details</label>
            <textarea id="body" name="body" rows="6" required class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('body') }}</textarea>
        </div>
        <x-select label="Priority" name="priority" :options="collect(App\Models\Ticket::PRIORITIES)->mapWithKeys(fn ($p) => [$p => ucfirst($p)])->all()" :selected="old('priority', 'normal')" />
        <x-select label="Assign to" name="assigned_to" :options="$supportUsers->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()" :selected="old('assigned_to')" placeholder="Unassigned" />
        <x-button type="submit" variant="primary">Create</x-button>
    </form>
</x-panel>
@endsection
