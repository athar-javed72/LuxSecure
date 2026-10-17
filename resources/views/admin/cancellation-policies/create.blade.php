@extends('admin.layout')

@section('title', 'Add Cancellation Policy')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Cancellation policies', 'href' => route('admin.cancellation-policies.index')],
    ['label' => 'Add'],
]" />

<x-page-header title="Add cancellation policy" />

<x-panel class="max-w-4xl">
    <form method="POST" action="{{ route('admin.cancellation-policies.store') }}">
        @csrf
        @include('admin.cancellation-policies._form', ['policy' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create policy</x-button>
            <x-button variant="ghost" href="{{ route('admin.cancellation-policies.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
