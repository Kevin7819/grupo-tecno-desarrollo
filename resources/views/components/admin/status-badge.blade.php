@props(['status'])

@php
    $value = $status instanceof \App\Domain\Cms\ProgramStatus ? $status : \App\Domain\Cms\ProgramStatus::fromString((string) $status);

    $classes = [
        'published' => 'border-success/40 bg-success/10 text-success',
        'draft' => 'border-warning/40 bg-warning/10 text-warning',
        'inactive' => 'border-surface-border bg-surface-border/40 text-ink-muted',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex shrink-0 items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold '.($classes[$value->value] ?? $classes['draft']),
]) }}>
    {{ $value->label() }}
</span>
