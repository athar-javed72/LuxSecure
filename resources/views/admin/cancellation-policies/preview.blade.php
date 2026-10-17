@extends('admin.layout')

@section('title', 'Policy Preview')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Cancellation policies', 'href' => route('admin.cancellation-policies.index')],
    ['label' => $policy->name],
    ['label' => 'Preview'],
]" />

<x-page-header title="Refund preview" :subtitle="$policy->name" />

<x-panel class="max-w-xl" :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Days before</th>
                <th class="px-3 py-3 font-semibold">Refund %</th>
                <th class="px-3 py-3 font-semibold">Fixed fee</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @foreach ($preview as $row)
                <tr>
                    <td class="px-3 py-2">{{ $row['days_before'] }}</td>
                    <td class="px-3 py-2">{{ $row['refund_percent'] }}%</td>
                    <td class="px-3 py-2">{{ $row['fee_fixed'] ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-panel>

<div class="mt-4">
    <x-button variant="ghost" href="{{ route('admin.cancellation-policies.index') }}">Back</x-button>
</div>
@endsection
