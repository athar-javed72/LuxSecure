@extends('admin.layout')

@section('title', 'Add agent profile')

@section('content')
<h1 class="text-xl font-bold mb-4">Add agent profile</h1>
<form method="POST" action="{{ route('admin.agents.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    @csrf
    <x-select label="User" name="user_id" :options="$users->mapWithKeys(fn ($u) => [$u->id => $u->name.' ('.$u->email.')'])->all()" :selected="old('user_id')" required />
    @include('admin.agents._fields')
    <x-button type="submit" variant="primary">Create</x-button>
</form>
@endsection
