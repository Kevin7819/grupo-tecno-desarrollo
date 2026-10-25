@extends('layouts.admin')

@section('title', 'Panel administrativo — Grupo Tecno Desarrollo')

@section('content')
    <h1 class="text-3xl font-semibold">Panel administrativo</h1>
    <p class="mt-2 text-ink-muted">
        Sesión iniciada como <span class="font-semibold text-ink">{{ auth('admin')->user()->name }}</span>.
    </p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <section class="rounded-2xl border border-surface-border bg-surface-card p-6">
            <h2 class="text-lg font-semibold">Cuenta</h2>
            <p class="mt-2 text-sm text-ink-muted">{{ auth('admin')->user()->email }}</p>
        </section>

        <section class="rounded-2xl border border-surface-border bg-surface-card p-6">
            <h2 class="text-lg font-semibold">Contenido</h2>
            <p class="mt-2 text-sm text-ink-muted">
                La gestión de programas y horarios se habilitará cuando existan datos aprobados.
            </p>
        </section>
    </div>
@endsection
