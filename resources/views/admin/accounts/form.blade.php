@extends('layouts.admin')

@section('title', $account === null ? 'Nueva cuenta administrativa' : 'Editar cuenta administrativa')

@section('content')
    @php
        $error = 'mt-1 text-xs text-danger';
    @endphp

    <h1 class="text-3xl font-semibold">{{ $account === null ? 'Nueva cuenta administrativa' : 'Editar cuenta administrativa' }}</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        La contraseña se guarda cifrada y nunca se muestra. Mínimo 8 caracteres.
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST"
          action="{{ $account === null ? route('admin.accounts.store') : route('admin.accounts.update', $account->id) }}"
          class="surface-card mt-8 grid max-w-2xl gap-4">
        @csrf
        @if ($account !== null)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block text-sm font-medium text-ink">Nombre</label>
            <x-ui.input name="name" :value="old('name', $account?->name)" maxlength="120" />
            @error('name')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-ink">Correo electrónico</label>
            <x-ui.input name="email" type="email" :value="old('email', $account?->email)" maxlength="255" />
            @error('email')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink">
                Contraseña{{ $account !== null ? ' (opcional)' : '' }}
            </label>
            <x-ui.input name="password" type="password" autocomplete="new-password" />
            <p class="mt-1 text-xs text-ink-muted">
                {{ $account !== null ? 'Dejá el campo vacío para conservar la contraseña actual.' : 'Mínimo 8 caracteres.' }}
            </p>
            @error('password')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-ink">Repetir contraseña</label>
            <x-ui.input name="password_confirmation" type="password" autocomplete="new-password" />
        </div>

        <div class="flex flex-wrap gap-3">
            <x-ui.button type="submit">{{ $account === null ? 'Crear cuenta' : 'Guardar cambios' }}</x-ui.button>
            <a href="{{ route('admin.accounts.index') }}" class="action-secondary">Volver a cuentas</a>
        </div>
    </form>
@endsection
