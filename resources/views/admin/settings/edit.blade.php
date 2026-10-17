@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<h1 class="text-xl font-bold mb-6">Site settings</h1>
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8 max-w-3xl">
    @csrf
    @method('PUT')
    @foreach ($grouped as $group => $settings)
        <section class="rounded-md bg-white border border-black/5 p-5 space-y-4">
            <h2 class="font-semibold capitalize">{{ str_replace('_', ' ', $group) }}</h2>
            @foreach ($settings as $setting)
                <div>
                    <label class="block text-xs font-semibold mb-1" for="s-{{ $setting->id }}">{{ $setting->label ?: $setting->key }}</label>
                    @if ($setting->type === 'text')
                        <textarea id="s-{{ $setting->id }}" name="settings[{{ $setting->key }}]" rows="4" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                    @else
                        <input id="s-{{ $setting->id }}" name="settings[{{ $setting->key }}]" value="{{ old('settings.'.$setting->key, $setting->value) }}" class="w-full h-12 px-4 rounded-sm border border-slate-200 text-sm">
                    @endif
                </div>
            @endforeach
        </section>
    @endforeach
    <x-button type="submit" variant="primary">Save settings</x-button>
</form>
@endsection
