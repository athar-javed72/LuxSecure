@extends('admin.layout')

@section('title', 'Add Language')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Languages', 'href' => route('admin.languages.index')],
    ['label' => 'Add'],
]" />

<x-page-header title="Add language" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.languages.store') }}">
        @csrf
        @include('admin.languages._form', ['language' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create language</x-button>
            <x-button variant="ghost" href="{{ route('admin.languages.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
