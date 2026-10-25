@props(['name', 'type' => 'text'])

<input
    {{ $attributes->merge([
        'type' => $type,
        'name' => $name,
        'id' => $attributes->get('id', $name),
        'class' => 'mt-1 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink placeholder:text-ink-muted/60',
    ]) }}
>
