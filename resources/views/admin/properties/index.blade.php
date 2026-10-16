@extends('admin.layout')

@section('title', 'Properties')

@section('content')
<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Properties</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Manage property listings</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
        <x-button variant="primary" href="{{ route('admin.properties.create') }}">Add property</x-button>
    </div>
</div>

<div class="rounded-md bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto xl:overflow-x-visible">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold xl:w-[8%]">Image</th>
                    <th class="px-3 py-3 font-semibold xl:w-[22%]">Title</th>
                    <th class="px-3 py-3 font-semibold xl:w-[18%]">Location</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Type</th>
                    <th class="px-3 py-3 font-semibold xl:w-[12%]">Price</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Status</th>
                    <th class="px-3 py-3 font-semibold text-right xl:w-[20%]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($properties as $p)
                    <tr>
                        <td class="px-3 py-3">
                            @if(filled($p->primary_image_url))
                                <img src="{{ $p->primary_image_url }}" alt="{{ $p->title }}" loading="lazy" width="64" height="48" class="w-14 h-10 object-cover rounded-sm">
                            @else
                                <div class="w-14 h-10 rounded-sm bg-slate-200"></div>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-medium text-fs-primary xl:truncate">{{ $p->title }}</td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">{{ $p->location }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $p->type }}</td>
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">PKR {{ number_format($p->price / 1_00_00_000, 1) }} Cr</td>
                        <td class="px-3 py-3">
                            <x-badge :tone="$p->is_active ? 'success' : 'muted'">{{ $p->is_active ? 'Active' : 'Inactive' }}</x-badge>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap justify-end gap-1">
                                <x-button variant="ghost" href="{{ route('admin.properties.show', $p) }}" class="!px-3">View</x-button>
                                <x-button variant="secondary" href="{{ route('admin.properties.edit', $p) }}" class="!px-3">Edit</x-button>
                                <form action="{{ route('admin.properties.destroy', $p) }}" method="POST" onsubmit="return confirm('Delete this property?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="ghost" class="!px-3 !text-fs-danger">Delete</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-fs-muted">No properties yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($properties->hasPages())
    <div class="mt-4">{{ $properties->links() }}</div>
@endif
@endsection
