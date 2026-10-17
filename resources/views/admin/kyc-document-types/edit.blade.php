@extends('admin.layout')

@section('title', 'Edit KYC Document Type')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'KYC document types', 'href' => route('admin.kyc-document-types.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit KYC document type" :subtitle="$type->name" />

<x-panel class="max-w-xl">
    <form method="POST" action="{{ route('admin.kyc-document-types.update', $type) }}">
        @csrf
        @method('PUT')
        @include('admin.kyc-document-types._form', ['type' => $type])
        <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Save changes</x-button>
            <x-button variant="ghost" href="{{ route('admin.kyc-document-types.index', ['country_id' => $type->country_id]) }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection
