@extends('admin.layout')

@section('title', 'Pages')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content', 'href' => route('admin.cms.index')],
    ['label' => 'Pages'],
]" />

<x-page-header title="Static pages" />

<x-panel class="mb-6 max-w-2xl">
    <h2 class="text-sm font-semibold text-fs-primary mb-3">Create page</h2>
    <form method="POST" action="{{ route('admin.cms.pages.store') }}" class="space-y-3">
        @csrf
        <x-input label="Title" name="title" required />
        <x-input label="Slug" name="slug" />
        <div>
            <label for="new-body" class="block text-xs font-semibold text-fs-primary mb-1">Body</label>
            <textarea id="new-body" name="body" rows="5" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20" placeholder="Body"></textarea>
        </div>
        <x-input label="Meta title" name="meta_title" />
        <x-input label="Meta description" name="meta_description" />
        <label class="text-sm inline-flex gap-2 items-center min-h-11"><input type="checkbox" name="is_published" value="1" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"> Published</label>
        <x-button type="submit" variant="primary">Create page</x-button>
    </form>
</x-panel>

@if($pages->isEmpty())
    <x-empty-state title="No pages yet" description="Create your first static page above." />
@else
    <div class="space-y-4 max-w-2xl">
        @foreach ($pages as $page)
            <x-panel>
                <form method="POST" action="{{ route('admin.cms.pages.update', $page) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <x-input label="Title" name="title" :value="$page->title" />
                    <x-input label="Slug" name="slug" :value="$page->slug" />
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Body</label>
                        <textarea name="body" rows="4" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ $page->body }}</textarea>
                    </div>
                    <x-input label="Meta title" name="meta_title" :value="$page->meta_title" />
                    <x-input label="Meta description" name="meta_description" :value="$page->meta_description" />
                    <label class="text-sm inline-flex gap-2 items-center min-h-11"><input type="checkbox" name="is_published" value="1" @checked($page->is_published) class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"> Published</label>
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
