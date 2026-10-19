@extends('include.master')

@section('title', 'My Profile')

@section('content')
<x-container class="py-8 md:py-12">
    <x-page-header
        title="My profile"
        subtitle="Manage your account and saved properties"
    />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        <x-panel>
            @if(isset($showEdit) && $showEdit)
                <h2 class="fs-display text-lg text-fs-primary mb-4">Update profile</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <x-input label="Name" name="name" :value="old('name', $user->name)" required />
                    <x-input label="Email" name="email" type="email" :value="old('email', $user->email)" required />
                    <x-input label="Phone" name="phone" :value="old('phone', $user->phone)" placeholder="Optional" />
                    <x-location-picker
                        :countries="\App\Models\Country::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])"
                        country-name="country_id"
                        city-name="city_id"
                        :country-id="old('country_id', $user->country_id)"
                        :required="false"
                        :allow-empty-country="true"
                        city-label-text="City (optional)"
                    />
                    <div class="flex flex-wrap gap-2">
                        <x-button type="submit" variant="primary">Save</x-button>
                        <x-button variant="ghost" href="{{ route('profile') }}">Cancel</x-button>
                    </div>
                </form>
            @else
                <h2 class="fs-display text-lg text-fs-primary mb-4">Profile information</h2>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Name</dt>
                        <dd class="mt-1 text-fs-primary">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Email</dt>
                        <dd class="mt-1 text-fs-primary">{{ $user->email }}</dd>
                    </div>
                    @if($user->phone)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Phone</dt>
                            <dd class="mt-1 text-fs-primary">{{ $user->phone }}</dd>
                        </div>
                    @endif
                </dl>
                <div class="mt-6 flex flex-wrap gap-2">
                    <x-button variant="secondary" href="{{ route('profile', ['edit' => 1]) }}">Edit profile</x-button>
                    <x-button variant="ghost" href="{{ route('kyc.index') }}">KYC</x-button>
                    <x-button variant="ghost" href="{{ route('bookings.mine') }}">My bookings</x-button>
                </div>
            @endif
        </x-panel>

        <x-panel class="bg-fs-surface/60">
            <h2 class="fs-display text-lg text-fs-primary">Quick links</h2>
            <div class="mt-4 space-y-2">
                <x-button variant="ghost" href="{{ route('properties') }}" class="w-full">Browse properties</x-button>
                <x-button variant="ghost" href="{{ route('notifications.index') }}" class="w-full">Notifications</x-button>
                <x-button variant="ghost" href="{{ route('contact') }}" class="w-full">Contact support</x-button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="ghost" class="w-full !text-fs-danger">Logout</x-button>
                </form>
            </div>
        </x-panel>
    </div>

    <section id="favorites" class="mt-12 scroll-mt-[72px]">
        <x-section-heading
            title="Saved properties"
            subtitle="Properties you have added to your wishlist"
            heading-id="favorites-heading"
        />
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($user->favorites as $property)
                <x-property-card :property="$property" />
            @empty
                <div class="col-span-full">
                    <x-empty-state
                        title="No saved properties yet"
                        description="Browse listings and tap the heart to save favorites here."
                        action-label="Browse properties"
                        :action-href="route('properties')"
                    />
                </div>
            @endforelse
        </div>
    </section>
</x-container>
@endsection
