@extends('admin.layout')

@section('title', 'Add KYC Document Type')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'KYC document types', 'href' => route('admin.kyc-document-types.index')],
    ['label' => 'Add'],
]" />

<x-page-header title="Add KYC document type" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.kyc-document-types.store') }}">
        @csrf
        @include('admin.kyc-document-types._form', ['type' => null])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create type</x-button>
            <x-button variant="ghost" href="{{ route('admin.kyc-document-types.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
