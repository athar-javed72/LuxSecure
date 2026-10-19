@extends('admin.layout')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Users', 'href' => route('admin.users.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit user">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.users.show', $user) }}">Back</x-button>
    </x-slot:actions>
</x-page-header>

<x-panel class="w-full max-w-5xl">
    <h2 class="fs-display text-base font-semibold text-fs-primary mb-6">{{ $user->name }}</h2>
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Name *" name="name" :value="old('name', $user->name)" required />
            <x-input label="Email *" name="email" type="email" :value="old('email', $user->email)" required />
            <x-input label="Phone" name="phone" :value="old('phone', $user->phone)" placeholder="03xx-xxxxxxx" />
            <x-input label="New password" name="password" type="password" placeholder="Leave blank to keep current" />
            <x-input label="Confirm password" name="password_confirmation" type="password" />
            <div class="md:col-span-2">
                <x-location-picker
                    :countries="\App\Models\Country::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])"
                    country-name="country_id"
                    city-name="city_id"
                    :country-id="old('country_id', $user->country_id)"
                    :required="false"
                    :allow-empty-country="true"
                    city-label-text="City (optional)"
                />
            </div>
            <label class="inline-flex items-center gap-2 min-h-11 md:col-span-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                <span class="text-sm text-fs-primary">Active</span>
            </label>
            @include('admin.users._rbac-fields')
        </div>
        <div class="mt-6 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Update</x-button>
            <x-button variant="ghost" href="{{ route('admin.users.show', $user) }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
