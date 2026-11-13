@extends('layouts.admin')

@section('title', 'Panel administrativo — Grupo Tecno Desarrollo')

@section('content')
    @php
        $activityLabels = [
            'settings.updated' => 'Configuración de la emisora actualizada',
            'program.created' => 'Programa creado',
            'program.updated' => 'Programa actualizado',
            'program.published' => 'Programa publicado',
            'program.inactivated' => 'Programa desactivado',
            'program.drafted' => 'Programa devuelto a borrador',
            'program.deleted' => 'Programa eliminado',
            'host.created' => 'Locutor creado',
            'host.updated' => 'Locutor actualizado',
            'host.deleted' => 'Locutor eliminado',
            'content.updated' => 'Contenido de la página pública actualizado',
            'gallery.created' => 'Imagen agregada a la galería',
            'gallery.updated' => 'Imagen de la galería actualizada',
            'gallery.deleted' => 'Imagen eliminada de la galería',
        ];

        $streamState = ! $station->status->isActive()
            ? $station->status->label()
            : ($station->stream->isConfigured() ? 'Transmisión configurada' : 'Transmisión sin configurar');

        $streamTone = $station->isBroadcasting()
            ? 'border-success/40 bg-success/10 text-success'
            : 'border-warning/40 bg-warning/10 text-warning';
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="section-eyebrow">Panel</p>
            <h1 class="mt-2 text-3xl font-semibold">
                {{ $station->stationName ?? 'Emisora sin nombre configurado' }}
            </h1>
            <p class="mt-2 text-ink-muted">
                Sesión iniciada como <span class="font-semibold text-ink">{{ auth('admin')->user()->name }}</span>.
            </p>
        </div>

        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ $streamTone }}">
            {{ $streamState }}
        </span>
    </div>

    @unless ($storageLinked)
        <x-ui.alert variant="error" class="mt-6">
            Las imágenes subidas todavía no son públicas: falta el enlace simbólico de almacenamiento.
            Ejecutá <code class="font-mono">php artisan storage:link</code> en el servidor.
        </x-ui.alert>
    @endunless

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <section class="surface-card">
            <h2 class="text-lg font-semibold">Emisora</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-ink-muted">Estado</dt>
                    <dd class="font-semibold">{{ $station->status->label() }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-ink-muted">Eslogan</dt>
                    <dd class="min-w-0 truncate text-right">{{ $station->slogan ?? 'Sin configurar' }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-ink-muted">Transmisión</dt>
                    <dd class="min-w-0 truncate text-right">
                        @if ($station->stream->isConfigured())
                            {{ $station->stream->name ?? 'Sin nombre' }}
                            <span class="block text-xs text-ink-muted">
                                {{ parse_url((string) $station->stream->url, PHP_URL_HOST) }} · {{ mb_strtoupper($station->stream->format->value) }}
                            </span>
                        @else
                            Sin configurar
                        @endif
                    </dd>
                </div>
            </dl>
            <a href="{{ route('admin.settings.edit') }}" class="action-secondary mt-5 w-full">Editar configuración</a>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Programación</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-ink-muted">Publicados</dt>
                    <dd class="font-semibold">{{ $publishedCount }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-ink-muted">Borradores</dt>
                    <dd class="font-semibold">{{ $draftCount }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-ink-muted">Inactivos</dt>
                    <dd class="font-semibold">{{ $inactiveCount }}</dd>
                </div>
            </dl>
            <a href="{{ route('admin.programs.index') }}" class="action-secondary mt-5 w-full">Administrar programación</a>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Locutores</h2>
            <p class="mt-4 text-3xl font-semibold">{{ $hostCount }}</p>
            <p class="text-sm text-ink-muted">locutores activos registrados</p>
            <a href="{{ route('admin.hosts.index') }}" class="action-secondary mt-5 w-full">Administrar locutores</a>
        </section>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <section class="surface-card">
            <h2 class="text-lg font-semibold">Página pública</h2>
            <p class="mt-4 text-sm text-ink-muted">
                Textos de portada, presentación, galería y contacto.
            </p>
            <a href="{{ route('admin.content.edit') }}" class="action-secondary mt-5 w-full">Editar contenido</a>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Galería</h2>
            <p class="mt-4 text-3xl font-semibold">{{ $galleryCount }}</p>
            <p class="text-sm text-ink-muted">imágenes publicadas</p>
            <a href="{{ route('admin.gallery.index') }}" class="action-secondary mt-5 w-full">Administrar galería</a>
        </section>
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-2">
        <section class="surface-card">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Próximos programas</h2>
                <a href="{{ route('admin.programs.create') }}" class="action-ghost">Nuevo programa</a>
            </div>

            @if ($upcoming === [])
                <p class="mt-4 text-sm text-ink-muted">
                    Todavía no hay programas publicados con horario. Cuando publiques uno, aparecerá acá.
                </p>
            @else
                <ul class="mt-4 divide-y divide-surface-border">
                    @foreach ($upcoming as $occurrence)
                        <li class="flex flex-wrap items-start justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <p class="font-semibold">{{ $occurrence->program->name }}</p>
                                <p class="text-sm text-ink-muted">{{ $occurrence->slot->label() }}</p>
                                @if ($occurrence->program->hostNames() !== [])
                                    <p class="text-xs text-ink-muted">Con {{ implode(', ', $occurrence->program->hostNames()) }}</p>
                                @endif
                            </div>
                            <span @class([
                                'shrink-0 rounded-full border px-2.5 py-0.5 text-xs font-semibold',
                                'border-brand/40 bg-brand-soft text-brand' => $occurrence->isOnAir,
                                'border-surface-border bg-surface-card text-ink-muted' => ! $occurrence->isOnAir,
                            ])>
                                @if ($occurrence->isOnAir)
                                    En emisión
                                @elseif ($occurrence->daysAhead === 0)
                                    Hoy
                                @elseif ($occurrence->daysAhead === 1)
                                    Mañana
                                @else
                                    En {{ $occurrence->daysAhead }} días
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Últimas actualizaciones</h2>

            @if ($activities === [])
                <p class="mt-4 text-sm text-ink-muted">Todavía no hay cambios registrados.</p>
            @else
                <ul class="mt-4 divide-y divide-surface-border">
                    @foreach ($activities as $activity)
                        <li class="py-3">
                            <p class="text-sm font-medium">{{ $activityLabels[$activity->action] ?? $activity->action }}</p>
                            <p class="text-xs text-ink-muted">
                                {{ $activity->subject }} · {{ $activity->actorName }} ·
                                {{ $activity->happenedAt->format('d/m/Y H:i') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <section class="surface-card mt-8">
        <h2 class="text-lg font-semibold">Accesos rápidos</h2>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('admin.programs.create') }}" class="action-primary">Nuevo programa</a>
            <a href="{{ route('admin.hosts.create') }}" class="action-secondary">Nuevo locutor</a>
            <a href="{{ route('admin.settings.edit') }}" class="action-secondary">Configuración de la emisora</a>
            <a href="{{ route('admin.content.edit') }}" class="action-secondary">Contenido público</a>
            <a href="{{ route('admin.gallery.create') }}" class="action-secondary">Nueva imagen</a>
        </div>
    </section>

    <p class="mt-8 text-xs text-ink-muted">
        Este panel no muestra oyentes, reproducciones ni audiencia: el proyecto todavía no mide esas métricas.
    </p>
@endsection
