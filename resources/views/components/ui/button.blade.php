@props(['variant' => 'primary'])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-60';

    $variants = [
        'primary' => 'bg-brand text-surface-base hover:bg-brand-strong',
        'secondary' => 'border border-surface-border bg-surface-card text-ink hover:border-brand hover:text-brand',
        'ghost' => 'text-ink-muted hover:text-ink',
    ];
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $base.' '.($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</button>
