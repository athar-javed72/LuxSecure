@extends('include.master')

@section('title', 'KYC verification')

@section('content')
<x-container class="py-10 max-w-2xl">
    <x-page-header
        title="KYC verification"
        subtitle="Upload CNIC, license, or registry documents. Files are stored on a private disk."
    />

    <x-panel>
        <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-select label="Document type" name="type" :options="['cnic' => 'CNIC', 'license' => 'License', 'registry' => 'Registry']" :selected="old('type')" required />
            <x-file-upload
                label="Document file"
                name="file"
                accept=".jpg,.jpeg,.png,.pdf"
                hint="JPG, PNG, or PDF."
                required
            />
            <x-button type="submit" variant="primary">Upload</x-button>
        </form>
    </x-panel>

    <div class="mt-8 space-y-3">
        @forelse($documents as $doc)
            <x-panel class="flex flex-wrap justify-between gap-3 text-sm">
                <div class="min-w-0">
                    <p class="font-medium uppercase text-fs-primary">{{ $doc->type }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$doc->status" />
                        <span class="text-xs text-fs-muted">{{ $doc->created_at->diffForHumans() }}</span>
                    </div>
                    @if($doc->reason)
                        <p class="text-fs-danger mt-2 text-xs">{{ $doc->reason }}</p>
                    @endif
                </div>
                @if(auth()->user()->isKycVerified())
                    <x-status-badge status="verified">Verified account</x-status-badge>
                @endif
            </x-panel>
        @empty
            <x-empty-state
                title="No documents yet"
                description="Upload at least one document to start KYC review."
            />
        @endforelse
    </div>
</x-container>
@endsection
