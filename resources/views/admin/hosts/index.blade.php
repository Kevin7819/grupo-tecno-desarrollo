@extends('layouts.admin')

@section('title', 'Locutores — Panel administrativo')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="section-eyebrow">Programación</p>
            <h1 class="mt-2 text-3xl font-semibold">Locutores</h1>
            <p class="mt-2 max-w-2xl text-ink-muted">
                Los locutores se asocian a los programas. Solo los locutores activos se muestran en la página pública.
            </p>
        </div>

        <a href="{{ route('admin.hosts.create') }}" class="action-primary">Nuevo locutor</a>
    </div>

    @if ($hosts === [])
        <div class="surface-card mt-8">
            <h2 class="text-lg font-semibold">Todavía no hay locutores</h2>
            <p class="mt-2 text-sm text-ink-muted">
                Registrá a las personas que conducen los programas para poder asociarlas a la programación.
            </p>
            <a href="{{ route('admin.hosts.create') }}" class="action-secondary mt-4">Crear locutor</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($hosts as $host)
                <article class="surface-card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex min-w-0 items-start gap-4">
                            @if ($host->photoPath !== null)
                                <img src="{{ Storage::disk('public')->url($host->photoPath) }}"
                                     alt="Foto de {{ $host->name }}"
                                     class="size-16 shrink-0 rounded-xl border border-surface-border object-cover">
                            @else
                                <span aria-hidden="true"
                                      class="flex size-16 shrink-0 items-center justify-center rounded-xl border border-surface-border bg-surface-base text-lg font-semibold text-ink-muted">
                                    {{ mb_strtoupper(mb_substr($host->name, 0, 1)) }}
                                </span>
                            @endif

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-semibold">{{ $host->name }}</h2>
                                    @if ($host->isActive)
                                        <span class="rounded-full border border-success/40 bg-success/10 px-2.5 py-0.5 text-xs font-semibold text-success">Activo</span>
                                    @else
                                        <span class="rounded-full border border-surface-border bg-surface-border/40 px-2.5 py-0.5 text-xs font-semibold text-ink-muted">Inactivo</span>
                                    @endif
                                </div>

                                @if ($host->bio !== null)
                                    <p class="mt-2 max-w-2xl text-sm text-ink-muted">{{ $host->bio }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.hosts.edit', $host->id) }}" class="action-secondary min-h-11 px-3 py-2">Editar</a>
                            <a href="{{ route('admin.hosts.delete', $host->id) }}" class="action-ghost">Eliminar</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
