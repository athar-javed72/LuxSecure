@extends('admin.layout')

@section('title', 'Pages')

@section('content')
@php
    $typeOptions = collect(\App\Models\Page::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all();
    $statusOptions = ['draft' => 'Draft', 'published' => 'Published'];
    $countries = \App\Models\Country::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id');
    $locales = \App\Models\Language::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'code');
@endphp

<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content', 'href' => route('admin.cms.index')],
    ['label' => 'Pages'],
]" />

<x-page-header title="Legal & static pages" subtitle="Country-aware pages with versioned publishing. Null country = global fallback." />

<x-panel class="mb-6 max-w-3xl">
    <h2 class="text-sm font-semibold text-fs-primary mb-3">Create page</h2>
    <form method="POST" action="{{ route('admin.cms.pages.store') }}" class="space-y-3">
        @csrf
        <x-input label="Title" name="title" required />
        <x-input label="Slug" name="slug" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-select label="Type" name="type" :options="$typeOptions" :selected="old('type', 'custom')" />
            <x-select label="Status" name="status" :options="$statusOptions" :selected="old('status', 'draft')" />
            <x-select label="Country" name="country_id" :options="$countries->all()" :selected="old('country_id')" placeholder="Global fallback" />
            <x-select label="Locale" name="locale" :options="$locales->all()" :selected="old('locale', 'en')" />
            <x-input label="Version" name="version" :value="old('version', '1.0')" />
            <x-input label="Effective from" name="effective_from" type="date" :value="old('effective_from')" />
        </div>
        <div>
            <label for="new-body" class="block text-xs font-semibold text-fs-primary mb-1">Body</label>
            <textarea id="new-body" name="body" rows="5" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20" placeholder="Body"></textarea>
        </div>
        <x-input label="Meta title" name="meta_title" />
        <x-input label="Meta description" name="meta_description" />
        <x-button type="submit" variant="primary">Create page</x-button>
    </form>
</x-panel>

@if($pages->isEmpty())
    <x-empty-state title="No pages yet" description="Create your first page above." />
@else
    <div class="space-y-4 max-w-3xl">
        @foreach ($pages as $page)
            <x-panel>
                <form method="POST" action="{{ route('admin.cms.pages.update', $page) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <div class="flex flex-wrap items-center gap-2 text-xs text-fs-muted mb-2">
                        <x-badge tone="muted">{{ $page->type ?: 'custom' }}</x-badge>
                        <span>{{ $page->country?->iso2 ?? 'GLOBAL' }}</span>
                        <span>{{ $page->locale }}</span>
                        <span>v{{ $page->version }}</span>
                        <x-status-badge :status="$page->status ?: ($page->is_published ? 'published' : 'draft')" />
                    </div>
                    <x-input label="Title" name="title" :value="$page->title" />
                    <x-input label="Slug" name="slug" :value="$page->slug" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-select label="Type" name="type" :options="$typeOptions" :selected="old('type', $page->type ?: 'custom')" />
                        <x-select label="Status" name="status" :options="$statusOptions" :selected="old('status', $page->status ?: 'draft')" />
                        <x-select label="Country" name="country_id" :options="$countries->all()" :selected="old('country_id', $page->country_id)" placeholder="Global fallback" />
                        <x-select label="Locale" name="locale" :options="$locales->all()" :selected="old('locale', $page->locale ?: 'en')" />
                        <x-input label="Version" name="version" :value="old('version', $page->version ?: '1.0')" />
                        <x-input label="Effective from" name="effective_from" type="date" :value="old('effective_from', optional($page->effective_from)->format('Y-m-d'))" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Body</label>
                        <textarea name="body" rows="4" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ $page->body }}</textarea>
                    </div>
                    <x-input label="Meta title" name="meta_title" :value="$page->meta_title" />
                    <x-input label="Meta description" name="meta_description" :value="$page->meta_description" />
                    <div class="flex flex-wrap gap-2 pt-2">
                        <x-button type="submit" variant="secondary">Save</x-button>
                        <x-button variant="ghost" href="{{ route('pages.show', $page->slug) }}" target="_blank">View</x-button>
                    </div>
                </form>
            </x-panel>
        @endforeach
    </div>
@endif
<div class="mt-4">{{ $pages->links() }}</div>
@endsection
