@extends('admin.layout')

@section('title', 'Agents')

@section('content')
<div class="flex flex-wrap justify-between gap-4 mb-6">
    <h1 class="text-xl font-bold">Agent profiles</h1>
    @can('create', App\Models\AgentProfile::class)
        <x-button variant="primary" href="{{ route('admin.agents.create') }}">Add profile</x-button>
    @endcan
</div>
<div class="rounded-md bg-white border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-xs uppercase text-fs-muted text-left">
            <tr>
                <th class="px-3 py-3">Name</th>
                <th class="px-3 py-3">Agency</th>
                <th class="px-3 py-3">Phone</th>
                <th class="px-3 py-3">Verified</th>
                <th class="px-3 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($profiles as $profile)
                <tr>
                    <td class="px-3 py-3 font-medium">{{ $profile->user->name }}</td>
                    <td class="px-3 py-3">{{ $profile->agency ?? '—' }}</td>
                    <td class="px-3 py-3">{{ $profile->phone ?? '—' }}</td>
                    <td class="px-3 py-3">{{ $profile->is_verified ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-3 text-right"><a href="{{ route('admin.agents.edit', $profile) }}" class="text-fs-brand hover:underline">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-fs-muted">No agent profiles.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $profiles->links() }}</div>
@endsection
