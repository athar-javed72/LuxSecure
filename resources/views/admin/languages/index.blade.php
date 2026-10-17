@extends('admin.layout')

@section('title', 'Languages')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Languages'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Languages</h1>
        <p class="mt-2 text-sm text-fs-secondary">Locales available for country defaults</p>
    </div>
    @can('languages.create')
        <x-button variant="primary" href="{{ route('admin.languages.create') }}">Add language</x-button>
    @endcan
</div>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Code</th>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">Native</th>
                <th class="px-3 py-3 font-semibold">RTL</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($languages as $language)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $language->code }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $language->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $language->native_name ?? '—' }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$language->is_rtl ? 'success' : 'muted'">{{ $language->is_rtl ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3"><x-badge :tone="$language->is_active ? 'success' : 'muted'">{{ $language->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            @can('languages.update')
                                <x-button variant="secondary" href="{{ route('admin.languages.edit', $language) }}" class="!px-3">Edit</x-button>
                            @endcan
                            @can('languages.delete')
                                <x-confirm-delete :action="route('admin.languages.destroy', $language)" label="Delete" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No languages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($languages->hasPages())
    <div class="mt-4">{{ $languages->links() }}</div>
@endif
@endsection
