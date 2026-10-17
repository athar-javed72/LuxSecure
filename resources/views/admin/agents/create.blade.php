@extends('admin.layout')

@section('title', 'Add agent profile')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Agents', 'href' => route('admin.agents.index')],
    ['label' => 'Create'],
]" />

<x-page-header title="Add agent profile" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.agents.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <x-select label="User" name="user_id" :options="$users->mapWithKeys(fn ($u) => [$u->id => $u->name.' ('.$u->email.')'])->all()" :selected="old('user_id')" required />
        @include('admin.agents._fields')
        <x-button type="submit" variant="primary">Create</x-button>
    </form>
</x-panel>
@endsection
