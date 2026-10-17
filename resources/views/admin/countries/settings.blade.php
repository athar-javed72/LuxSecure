@extends('admin.layout')

@section('title', 'Country Settings')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Countries', 'href' => route('admin.countries.index')],
    ['label' => $country->name],
    ['label' => 'Settings'],
]" />

<x-page-header title="Country settings" :subtitle="$country->name" />

<form method="POST" action="{{ route('admin.countries.settings.update', $country) }}" class="space-y-6 max-w-3xl">
    @csrf
    @method('PUT')
    <x-panel class="space-y-4">
        @foreach ($settings as $setting)
            <div>
                <label class="block text-xs font-semibold text-fs-primary mb-1" for="s-{{ Str::slug($setting['key']) }}">{{ $setting['label'] }}</label>
                @if ($setting['type'] === 'text')
                    <textarea id="s-{{ Str::slug($setting['key']) }}" name="settings[{{ $setting['key'] }}]" rows="3" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('settings.'.$setting['key'], $setting['value']) }}</textarea>
                @else
                    <input id="s-{{ Str::slug($setting['key']) }}" name="settings[{{ $setting['key'] }}]" value="{{ old('settings.'.$setting['key'], $setting['value']) }}" class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">
                @endif
            </div>
        @endforeach
    </x-panel>
    <div class="flex flex-wrap gap-2">
        <x-button type="submit" variant="primary">Save settings</x-button>
        <x-button variant="ghost" href="{{ route('admin.countries.index') }}">Back</x-button>
    </div>
</form>
@endsection
