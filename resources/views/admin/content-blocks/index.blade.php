@extends('admin.layout')

@section('title', 'Content blocks')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content blocks'],
]" />

<x-page-header title="Content blocks" subtitle="Editorial copy for Home and division pages. Empty fields fall back to neutral language defaults." />

<form method="POST" action="{{ route('admin.content-blocks.update') }}" class="space-y-4 max-w-3xl">
    @csrf
    @method('PUT')
    @foreach($keys as $key)
        @php
            $defaults = trans('content.'.$key);
            $row = $existing->get($key);
            $defaultTitle = is_array($defaults) ? ($defaults['title'] ?? '') : '';
            $defaultBody = is_array($defaults) ? ($defaults['body'] ?? '') : '';
        @endphp
        <x-panel class="!p-4">
            <h2 class="text-sm font-semibold text-fs-primary mb-3"><code class="text-xs">{{ $key }}</code></h2>
            <label class="block text-xs font-semibold mb-1">Title
                <input type="text" name="blocks[{{ $key }}][title]" value="{{ old('blocks.'.$key.'.title', $row?->title) }}" placeholder="{{ $defaultTitle }}" class="mt-1 w-full border border-black/10 rounded-sm px-3 py-2 text-sm">
            </label>
            <label class="block text-xs font-semibold mb-1 mt-3">Body
                <textarea name="blocks[{{ $key }}][body]" rows="3" placeholder="{{ $defaultBody }}" class="mt-1 w-full border border-black/10 rounded-sm px-3 py-2 text-sm">{{ old('blocks.'.$key.'.body', $row?->body) }}</textarea>
            </label>
        </x-panel>
    @endforeach
    <x-button type="submit" variant="primary">Save content blocks</x-button>
</form>
@endsection
