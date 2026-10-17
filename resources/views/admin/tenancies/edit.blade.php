@extends('admin.layout')

@section('title', 'Edit tenancy')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies', 'href' => route('admin.tenancies.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit tenancy" />

<x-panel class="max-w-2xl">
    <form method="POST" action="{{ route('admin.tenancies.update', $tenancy) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Start date" name="start_date" type="date" :value="old('start_date', $tenancy->start_date->toDateString())" required />
            <x-input label="End date" name="end_date" type="date" :value="old('end_date', optional($tenancy->end_date)->toDateString())" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Monthly rent" name="monthly_rent" type="number" step="0.01" :value="old('monthly_rent', $tenancy->monthly_rent)" required />
            <x-input label="Security deposit" name="security_deposit" type="number" step="0.01" :value="old('security_deposit', $tenancy->security_deposit)" />
        </div>
        <x-select
            label="Status"
            name="status"
            :options="collect(App\Models\Tenancy::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()"
            :selected="old('status', $tenancy->status)"
        />
        <div class="flex flex-wrap gap-2 pt-2">
            <x-button type="submit" variant="primary">Save</x-button>
            <x-button variant="ghost" href="{{ route('admin.tenancies.show', $tenancy) }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
