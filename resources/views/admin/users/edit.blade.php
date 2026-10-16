@extends('admin.layout')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Users', 'href' => route('admin.users.index')],
    ['label' => 'Edit'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Edit user</h1>
    <x-button variant="ghost" href="{{ route('admin.users.show', $user) }}">Back</x-button>
</div>

<div class="rounded-md bg-white shadow-sm border border-black/5 p-6 w-full max-w-5xl">
    <h2 class="text-base font-semibold text-fs-primary mb-6">{{ $user->name }}</h2>
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Name *" name="name" :value="old('name', $user->name)" required />
            <x-input label="Email *" name="email" type="email" :value="old('email', $user->email)" required />
            <x-input label="Phone" name="phone" :value="old('phone', $user->phone)" placeholder="03xx-xxxxxxx" />
            <x-select
                label="Role *"
                name="role"
                :options="['user' => 'User', 'admin' => 'Admin']"
                :selected="old('role', $user->role)"
                required
            />
            <x-input label="New password" name="password" type="password" placeholder="Leave blank to keep current" />
            <x-input label="Confirm password" name="password_confirmation" type="password" />
        </div>
        <div class="mt-6 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Update</x-button>
            <x-button variant="ghost" href="{{ route('admin.users.show', $user) }}">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
