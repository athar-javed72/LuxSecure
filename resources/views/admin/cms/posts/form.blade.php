@extends('admin.layout')

@section('title', $post->exists ? 'Edit post' : 'New post')

@section('content')
<h1 class="text-xl font-bold mb-4">{{ $post->exists ? 'Edit post' : 'New post' }}</h1>
<form method="POST" action="{{ $post->exists ? route('admin.cms.posts.update', $post) : route('admin.cms.posts.store') }}" class="max-w-2xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    @csrf
    @if($post->exists) @method('PUT') @endif
    <x-input label="Title" name="title" :value="old('title', $post->title)" required />
    <x-input label="Slug" name="slug" :value="old('slug', $post->slug)" />
    <x-input label="Category" name="category" :value="old('category', $post->category)" />
    <x-input label="Excerpt" name="excerpt" :value="old('excerpt', $post->excerpt)" />
    <div>
        <label class="block text-xs font-semibold mb-1">Body</label>
        <textarea name="body" rows="8" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ old('body', $post->body) }}</textarea>
    </div>
    <x-input label="Featured image URL" name="featured_image" :value="old('featured_image', $post->featured_image)" />
    <x-select label="Status" name="status" :options="['draft' => 'Draft', 'published' => 'Published']" :selected="old('status', $post->status ?: 'draft')" />
    <x-input label="Published at" name="published_at" type="datetime-local" :value="old('published_at', optional($post->published_at)->format('Y-m-d\TH:i'))" />
    <x-input label="Meta title" name="meta_title" :value="old('meta_title', $post->meta_title)" />
    <x-input label="Meta description" name="meta_description" :value="old('meta_description', $post->meta_description)" />
    <x-button type="submit" variant="primary">Save</x-button>
</form>
@endsection
