@extends('layouts.admin')

@section('title', 'Cuentas administrativas — Panel administrativo')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="section-eyebrow">Cuentas</p>
            <h1 class="mt-2 text-3xl font-semibold">Cuentas administrativas</h1>
            <p class="mt-2 max-w-2xl text-ink-muted">
                Todas las cuentas tienen el mismo nivel de acceso: el proyecto no usa roles ni permisos.
            </p>
        </div>

        <a href="{{ route('admin.accounts.create') }}" class="action-primary">Nueva cuenta</a>
    </div>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">{{ $errors->first() }}</x-ui.alert>
    @endif

    <div class="mt-8 space-y-4">
        @foreach ($accounts as $account)
            <article class="surface-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-semibold">{{ $account->name }}</h2>
                            @if ($account->id === $currentId)
                                <span class="inline-flex items-center rounded-full border border-brand/40 bg-brand-soft px-2.5 py-0.5 text-xs font-semibold text-ink">Tu cuenta</span>
                            @endif
                        </div>
                        <p class="mt-1 break-all text-sm text-ink-muted">{{ $account->email }}</p>
                        @if ($account->createdAt !== null)
                            <p class="mt-1 text-xs text-ink-muted">Creada el {{ $account->createdAt->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.accounts.edit', $account->id) }}" class="action-secondary min-h-11 px-3 py-2">Editar</a>
                        @if ($account->id !== $currentId)
                            <a href="{{ route('admin.accounts.delete', $account->id) }}" class="action-ghost">Eliminar</a>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endsection
