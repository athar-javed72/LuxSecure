@extends('admin.layout')

@section('title', 'Edit agent')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Agents', 'href' => route('admin.agents.index')],
    ['label' => $profile->user->name],
]" />

<x-page-header :title="$profile->user->name" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.agents.update', $profile) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.agents._fields', ['profile' => $profile])
        <x-button type="submit" variant="primary">Save</x-button>
    </form>
</x-panel>
@endsection
