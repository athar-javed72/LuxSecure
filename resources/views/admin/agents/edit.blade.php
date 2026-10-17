@extends('admin.layout')

@section('title', 'Edit agent')

@section('content')
<h1 class="text-xl font-bold mb-4">{{ $profile->user->name }}</h1>
<form method="POST" action="{{ route('admin.agents.update', $profile) }}" enctype="multipart/form-data" class="max-w-xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    @csrf
    @method('PUT')
    @include('admin.agents._fields', ['profile' => $profile])
    <x-button type="submit" variant="primary">Save</x-button>
</form>
@endsection
