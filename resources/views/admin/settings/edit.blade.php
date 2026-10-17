@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Settings'],
]" />

<x-page-header title="Site settings" subtitle="Configure branding, contact, and feature toggles" />

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 max-w-3xl">
    @csrf
    @method('PUT')
    @foreach ($grouped as $group => $settings)
        <x-panel class="space-y-4">
            <h2 class="fs-display text-base font-semibold text-fs-primary capitalize">{{ str_replace('_', ' ', $group) }}</h2>
            @foreach ($settings as $setting)
                <div>
                    <label class="block text-xs font-semibold text-fs-primary mb-1" for="s-{{ $setting->id }}">{{ $setting->label ?: $setting->key }}</label>
                    @if ($setting->type === 'text')
                        <textarea id="s-{{ $setting->id }}" name="settings[{{ $setting->key }}]" rows="4" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                    @else
                        <input id="s-{{ $setting->id }}" name="settings[{{ $setting->key }}]" value="{{ old('settings.'.$setting->key, $setting->value) }}" class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">
                    @endif
                </div>
            @endforeach
        </x-panel>
    @endforeach
    <x-button type="submit" variant="primary">Save settings</x-button>
</form>
@endsection
