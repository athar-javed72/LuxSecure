@extends('admin.layout')

@section('title', 'Pages')

@section('content')
<h1 class="text-xl font-bold mb-4">Static pages</h1>
<form method="POST" action="{{ route('admin.cms.pages.store') }}" class="mb-6 space-y-3 rounded-md bg-white border border-black/5 p-4 max-w-2xl">
    @csrf
    <x-input label="Title" name="title" required />
    <x-input label="Slug" name="slug" />
    <textarea name="body" rows="5" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm" placeholder="Body"></textarea>
    <x-input label="Meta title" name="meta_title" />
    <x-input label="Meta description" name="meta_description" />
    <label class="text-sm inline-flex gap-2"><input type="checkbox" name="is_published" value="1"> Published</label>
    <x-button type="submit" variant="primary">Create page</x-button>
</form>
@foreach ($pages as $page)
    <form method="POST" action="{{ route('admin.cms.pages.update', $page) }}" class="mb-4 rounded-md bg-white border border-black/5 p-4 space-y-2 max-w-2xl">
        @csrf @method('PUT')
        <x-input label="Title" name="title" :value="$page->title" />
        <x-input label="Slug" name="slug" :value="$page->slug" />
        <textarea name="body" rows="4" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ $page->body }}</textarea>
        <x-input label="Meta title" name="meta_title" :value="$page->meta_title" />
        <x-input label="Meta description" name="meta_description" :value="$page->meta_description" />
        <label class="text-sm inline-flex gap-2"><input type="checkbox" name="is_published" value="1" @checked($page->is_published)> Published</label>
        <div class="flex gap-2">
            <x-button type="submit" variant="secondary">Save</x-button>
            <a href="{{ route('pages.show', $page->slug) }}" class="text-sm text-fs-brand self-center" target="_blank">View</a>
        </div>
    </form>
@endforeach
<div class="mt-4">{{ $pages->links() }}</div>
@endsection
