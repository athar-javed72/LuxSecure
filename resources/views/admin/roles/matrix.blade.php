@extends('admin.layout')

@section('title', 'Permission Matrix')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Roles', 'href' => route('admin.roles.index')],
    ['label' => $role->name.' matrix'],
]" />

<x-page-header
    title="Permission matrix"
    :subtitle="'Role: '.$role->name.' — grant capabilities in clear groups. Use select-all per group, then save.'"
/>

<form method="POST" action="{{ route('admin.roles.matrix.update', $role) }}" class="pb-24">
    @csrf
    @method('PUT')

    <x-panel class="!p-0 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-black/5 bg-fs-surface/50">
            <p class="text-sm text-fs-secondary">Grouped by domain. Changes apply only after you save.</p>
        </div>
        <div class="p-4 sm:p-6">
            @include('admin.roles._permission-matrix', ['selected' => $selected])
        </div>
    </x-panel>

    <div class="fs-sticky-save mt-8 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-fs-muted">Sticky save — review groups above before confirming.</p>
        <div class="flex flex-wrap gap-2">
            <x-button variant="ghost" href="{{ route('admin.roles.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Save permissions</x-button>
        </div>
    </div>
</form>
@endsection
