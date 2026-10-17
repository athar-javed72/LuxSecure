@extends('include.master')

@section('title', 'KYC verification')

@section('content')
<x-container class="py-10 max-w-2xl">
    <h1 class="fs-display text-3xl text-fs-primary">KYC verification</h1>
    <p class="mt-2 text-sm text-fs-muted">Upload CNIC, license, or registry documents. Files are stored on a private disk.</p>

    @if(session('success'))
        <div class="mt-4 p-3 rounded-sm bg-fs-brand-light text-sm">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4 rounded-md bg-white border border-black/5 p-6">
        @csrf
        <x-select label="Document type" name="type" :options="['cnic' => 'CNIC', 'license' => 'License', 'registry' => 'Registry']" :selected="old('type')" required />
        <div>
            <label class="block text-xs font-semibold text-fs-primary mb-1">File *</label>
            <input type="file" name="file" required accept=".jpg,.jpeg,.png,.pdf" class="w-full h-12 px-3 rounded-sm border border-black/10 text-sm">
            @error('file')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
        </div>
        <x-button type="submit" variant="primary">Upload</x-button>
    </form>

    <div class="mt-8 space-y-3">
        @forelse($documents as $doc)
            <div class="rounded-md border border-black/5 bg-white p-4 text-sm flex justify-between gap-3">
                <div>
                    <p class="font-medium uppercase">{{ $doc->type }}</p>
                    <p class="text-fs-muted">{{ $doc->status }} · {{ $doc->created_at->diffForHumans() }}</p>
                    @if($doc->reason)<p class="text-fs-danger mt-1">{{ $doc->reason }}</p>@endif
                </div>
                @if(auth()->user()->isKycVerified())
                    <span class="text-xs font-semibold text-fs-brand">Verified</span>
                @endif
            </div>
        @empty
            <p class="text-sm text-fs-muted">No documents uploaded yet.</p>
        @endforelse
    </div>
</x-container>
@endsection
