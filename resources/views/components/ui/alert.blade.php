@props(['variant' => 'status'])

@php
    $variants = [
        'status' => 'border-brand/40 bg-brand-soft text-ink',
        'error' => 'border-danger/40 bg-danger/10 text-danger',
    ];
@endphp

<div {{ $attributes->merge([
    'class' => 'rounded-lg border px-4 py-3 text-sm '.($variants[$variant] ?? $variants['status']),
    'role' => $variant === 'error' ? 'alert' : 'status',
]) }}>
    {{ $slot }}
</div>
