@extends('admin.layout')

@section('title', 'Edit Cancellation Policy')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Cancellation policies', 'href' => route('admin.cancellation-policies.index')],
    ['label' => 'Edit'],
]" />

<x-page-header title="Edit cancellation policy" :subtitle="$policy->name" />

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <x-panel class="xl:col-span-2">
        <form method="POST" action="{{ route('admin.cancellation-policies.update', $policy) }}">
            @csrf
            @method('PUT')
            @include('admin.cancellation-policies._form', ['policy' => $policy])
            <div class="mt-8 pt-6 border-t border-black/5 flex flex-wrap gap-2">
                <x-button type="submit" variant="primary">Save changes</x-button>
                <x-button variant="ghost" href="{{ route('admin.cancellation-policies.index') }}">Cancel</x-button>
            </div>
        </form>
    </x-panel>

    <x-panel>
        <h2 class="text-sm font-semibold text-fs-primary mb-3">Refund preview (30 days)</h2>
        <div class="max-h-96 overflow-auto text-sm">
            <table class="w-full">
                <thead class="bg-fs-surface text-xs uppercase text-fs-muted sticky top-0">
                    <tr>
                        <th class="px-2 py-2 text-left">Days</th>
                        <th class="px-2 py-2 text-left">Refund %</th>
                        <th class="px-2 py-2 text-left">Fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($preview as $row)
                        <tr>
                            <td class="px-2 py-1.5">{{ $row['days_before'] }}</td>
                            <td class="px-2 py-1.5">{{ $row['refund_percent'] }}%</td>
                            <td class="px-2 py-1.5">{{ $row['fee_fixed'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
</div>
@endsection
