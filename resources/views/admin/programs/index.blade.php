@extends('layouts.admin')

@section('title', 'Programación — Panel administrativo')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="section-eyebrow">Programación</p>
            <h1 class="mt-2 text-3xl font-semibold">Programas</h1>
            <p class="mt-2 max-w-2xl text-ink-muted">
                Solo los programas publicados aparecen en la página pública. Un programa puede emitirse varios días por semana.
            </p>
        </div>

        <a href="{{ route('admin.programs.create') }}" class="action-primary">Nuevo programa</a>
    </div>

    @if ($programs === [])
        <div class="surface-card mt-8">
            <h2 class="text-lg font-semibold">Todavía no hay programas</h2>
            <p class="mt-2 text-sm text-ink-muted">
                Creá el primer programa, asignale sus franjas horarias y publicalo cuando esté confirmado.
            </p>
            <a href="{{ route('admin.programs.create') }}" class="action-secondary mt-4">Crear programa</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($programs as $program)
                <article class="surface-card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold">{{ $program->name }}</h2>
                                <x-admin.status-badge :status="$program->status" />
                            </div>
                            <p class="mt-1 text-xs text-ink-muted">Orden de visualización: {{ $program->sortOrder }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if ($program->isPublished())
                                <form method="POST" action="{{ route('admin.programs.status', $program->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="inactive">
                                    <x-ui.button variant="secondary" class="px-3 py-2">Desactivar</x-ui.button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.programs.status', $program->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="published">
                                    <x-ui.button class="px-3 py-2">Publicar</x-ui.button>
                                </form>
                            @endif

                            <a href="{{ route('admin.programs.edit', $program->id) }}" class="action-secondary min-h-11 px-3 py-2">Editar</a>
                            <a href="{{ route('admin.programs.delete', $program->id) }}" class="action-ghost">Eliminar</a>
                        </div>
                    </div>

                    @if ($program->coverPath !== null)
                        <img src="{{ Storage::disk('public')->url($program->coverPath) }}"
                             alt="Portada de {{ $program->name }}"
                             class="mt-4 h-24 w-auto rounded-lg border border-surface-border object-cover">
                    @endif

                    @if ($program->schedule !== [])
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach ($program->schedule as $slot)
                                <li class="chip">{{ $slot->label() }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 text-sm text-warning">Sin franjas horarias: no se puede publicar con sentido hasta definir el horario.</p>
                    @endif

                    @if ($program->hostNames() !== [])
                        <p class="mt-3 text-sm text-ink-muted">
                            Locutores: <span class="text-ink">{{ implode(', ', $program->hostNames()) }}</span>
                        </p>
                    @endif

                    @if ($program->description !== null)
                        <p class="mt-3 text-sm text-ink-muted">{{ $program->description }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection
