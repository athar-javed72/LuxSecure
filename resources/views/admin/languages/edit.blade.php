@extends('admin.layout')

@section('title', 'Edit Language')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Languages', 'href' => route('admin.languages.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit language" :subtitle="$language->name" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.languages.update', $language) }}">
        @csrf
        @method('PUT')
        @include('admin.languages._form', ['language' => $language])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.languages.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
