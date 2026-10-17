@extends('admin.layout')

@section('title', 'Create User')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Users', 'href' => route('admin.users.index')],
    ['label' => 'Create'],
]" />

<x-page-header title="Create user" subtitle="Staff and agents are created here; public registration is client-only." />

<x-panel class="w-full max-w-5xl">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Name *" name="name" :value="old('name')" required />
            <x-input label="Email *" name="email" type="email" :value="old('email')" required />
            <x-input label="Phone" name="phone" :value="old('phone')" />
            <x-input label="Password *" name="password" type="password" required />
            <x-input label="Confirm password *" name="password_confirmation" type="password" required />
            <label class="inline-flex items-center gap-2 min-h-11 md:col-span-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                <span class="text-sm text-fs-primary">Active</span>
            </label>
            @include('admin.users._rbac-fields', ['selectedRoles' => [], 'selectedPermissions' => []])
        </div>
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create user</x-button>
            <x-button variant="ghost" href="{{ route('admin.users.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
