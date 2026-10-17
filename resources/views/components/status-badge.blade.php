@props([
    'status' => 'pending',
])

@php
    $key = strtolower((string) $status);
    $map = [
        'pending' => 'muted',
        'draft' => 'muted',
        'awaiting_approval' => 'warning',
        'confirmed' => 'brand',
        'published' => 'success',
        'active' => 'success',
        'paid' => 'success',
        'approved' => 'success',
        'completed' => 'success',
        'verified' => 'success',
        'failed' => 'danger',
        'rejected' => 'danger',
        'cancelled' => 'danger',
        'overdue' => 'danger',
        'refund_pending' => 'warning',
        'refunded' => 'muted',
        'inactive' => 'muted',
        'open' => 'brand',
        'closed' => 'muted',
        'in_progress' => 'warning',
        'new' => 'brand',
        'contacted' => 'brand',
        'won' => 'success',
        'lost' => 'danger',
    ];
    $tone = $map[$key] ?? 'muted';
    $tones = [
        'brand' => 'bg-fs-brand-light text-fs-primary',
        'success' => 'bg-fs-accent/10 text-fs-accent',
        'warning' => 'bg-fs-brand/15 text-fs-brand-dark',
        'danger' => 'bg-fs-danger/10 text-fs-danger',
        'muted' => 'bg-black/5 text-fs-secondary',
    ];
    $label = ucwords(str_replace('_', ' ', $key));
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 h-7 rounded-sm text-xs font-semibold tracking-wide whitespace-nowrap '.($tones[$tone] ?? $tones['muted'])]) }}>
    {{ $slot->isEmpty() ? $label : $slot }}
</span>
