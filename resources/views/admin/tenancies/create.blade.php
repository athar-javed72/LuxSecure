@extends('admin.layout')

@section('title', 'New tenancy')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies', 'href' => route('admin.tenancies.index')],
    ['label' => 'Create'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Create tenancy</h1>
    <p class="mt-2 text-sm text-fs-secondary">A monthly rent schedule is generated automatically</p>
</div>

<form method="POST" action="{{ route('admin.tenancies.store') }}" class="max-w-2xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    @csrf
    <x-select label="Listing" name="listing_id" :options="$listings->mapWithKeys(fn ($l) => [$l->id => $l->title])->all()" :selected="old('listing_id')" required />
    <x-select label="Tenant" name="tenant_id" :options="$tenants->mapWithKeys(fn ($t) => [$t->id => $t->name.' ('.$t->email.')'])->all()" :selected="old('tenant_id')" required />
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input label="Start date" name="start_date" type="date" :value="old('start_date')" required />
        <x-input label="End date" name="end_date" type="date" :value="old('end_date')" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input label="Monthly rent" name="monthly_rent" type="number" step="0.01" :value="old('monthly_rent')" required />
        <x-input label="Security deposit" name="security_deposit" type="number" step="0.01" :value="old('security_deposit', 0)" />
    </div>
    <div class="flex gap-2">
        <x-button type="submit" variant="primary">Create & generate schedule</x-button>
        <x-button variant="ghost" href="{{ route('admin.tenancies.index') }}">Cancel</x-button>
    </div>
</form>
@endsection
