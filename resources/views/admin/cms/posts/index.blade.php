@extends('admin.layout')

@section('title', 'Blog posts')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content', 'href' => route('admin.cms.index')],
    ['label' => 'Blog posts'],
]" />

<x-page-header title="Blog posts">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('admin.cms.posts.create') }}">New post</x-button>
    </x-slot:actions>
</x-page-header>

@if($posts->isEmpty())
    <x-empty-state title="No posts yet" action-label="New post" :action-href="route('admin.cms.posts.create')" />
@else
    <x-table caption="Blog posts" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td data-label="Title">{{ $post->title }}</td>
                        <td data-label="Status"><x-status-badge :status="$post->status" /></td>
                        <td data-label="Actions" class="text-right space-x-2">
                            <x-button variant="ghost" href="{{ route('admin.cms.posts.edit', $post) }}" class="!px-3">Edit</x-button>
                            <form class="inline" method="POST" action="{{ route('admin.cms.posts.destroy', $post) }}">
                                @csrf @method('DELETE')
                                <x-button type="submit" variant="ghost" class="!px-3 !text-fs-danger">Delete</x-button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $posts->links() }}</div>
@endsection
