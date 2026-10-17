@extends('admin.layout')

@section('title', 'Blog posts')

@section('content')
<div class="flex justify-between mb-6">
    <h1 class="text-xl font-bold">Blog posts</h1>
    <x-button variant="primary" href="{{ route('admin.cms.posts.create') }}">New post</x-button>
</div>
<div class="rounded-md bg-white border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-xs uppercase text-left text-fs-muted"><tr><th class="px-3 py-3">Title</th><th class="px-3 py-3">Status</th><th class="px-3 py-3"></th></tr></thead>
        <tbody class="divide-y divide-black/5">
            @foreach ($posts as $post)
                <tr>
                    <td class="px-3 py-3">{{ $post->title }}</td>
                    <td class="px-3 py-3 capitalize">{{ $post->status }}</td>
                    <td class="px-3 py-3 text-right space-x-2">
                        <a href="{{ route('admin.cms.posts.edit', $post) }}" class="text-fs-brand hover:underline">Edit</a>
                        <form class="inline" method="POST" action="{{ route('admin.cms.posts.destroy', $post) }}">@csrf @method('DELETE')<button class="text-fs-danger text-sm">Delete</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $posts->links() }}</div>
@endsection
