@extends('layouts.plain')

@section('title', 'Restablecer contraseña — Grupo Tecno Desarrollo')

@section('content')
    <div class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-lg shadow-black/20 sm:p-8">
        <h1 class="text-2xl font-semibold">Restablecer contraseña</h1>
        <p class="mt-2 text-sm text-ink-muted">Define una nueva contraseña para tu cuenta.</p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                <x-ui.input name="email" type="email" :value="old('email', $email)" required autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Nueva contraseña</label>
                <x-ui.input name="password" type="password" required autofocus autocomplete="new-password" />
                @error('password')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Repite la nueva contraseña</label>
                <x-ui.input name="password_confirmation" type="password" required autocomplete="new-password" />
            </div>

            <x-ui.button class="w-full">Guardar contraseña</x-ui.button>
        </form>
    </div>
@endsection
