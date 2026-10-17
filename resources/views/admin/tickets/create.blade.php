@extends('admin.layout')

@section('title', 'New ticket')

@section('content')
<h1 class="text-xl font-bold mb-4">New support ticket</h1>
<form method="POST" action="{{ route('admin.tickets.store') }}" class="max-w-xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    @csrf
    <x-input label="Subject" name="subject" :value="old('subject')" required />
    <div>
        <label class="block text-xs font-semibold mb-1">Details</label>
        <textarea name="body" rows="6" required class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ old('body') }}</textarea>
    </div>
    <x-select label="Priority" name="priority" :options="collect(App\Models\Ticket::PRIORITIES)->mapWithKeys(fn ($p) => [$p => ucfirst($p)])->all()" :selected="old('priority', 'normal')" />
    <x-select label="Assign to" name="assigned_to" :options="$supportUsers->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()" :selected="old('assigned_to')" placeholder="Unassigned" />
    <x-button type="submit" variant="primary">Create</x-button>
</form>
@endsection
