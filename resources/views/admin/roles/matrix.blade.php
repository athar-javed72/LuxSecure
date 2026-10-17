@extends('admin.layout')

@section('title', 'Permission Matrix')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Roles', 'href' => route('admin.roles.index')],
    ['label' => $role->name.' matrix'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Permission matrix</h1>
    <p class="mt-2 text-sm text-fs-secondary">Role: {{ $role->name }}</p>
</div>

@if(session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="rounded-md bg-white shadow-sm border border-black/5 p-6 w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.roles.matrix.update', $role) }}">
        @csrf
        @method('PUT')
        @include('admin.roles._permission-matrix', ['selected' => $selected])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save permissions</x-button>
            <x-button variant="ghost" href="{{ route('admin.roles.index') }}">Back</x-button>
        </div>
    </form>
</div>
@endsection
