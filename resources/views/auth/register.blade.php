@extends('layouts.plain')

@section('title', 'Crear cuenta — Grupo Tecno Desarrollo')

@section('content')
    <div class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-lg shadow-black/20 sm:p-8">
        <h1 class="text-2xl font-semibold">Crear cuenta</h1>
        <p class="mt-2 text-sm text-ink-muted">Registra tu cuenta de oyente.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium">Nombre</label>
                <x-ui.input name="name" :value="old('name')" required autofocus autocomplete="name" />
                @error('name')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                <x-ui.input name="email" type="email" :value="old('email')" required autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Contraseña</label>
                <x-ui.input name="password" type="password" required autocomplete="new-password" />
                @error('password')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Repite la contraseña</label>
                <x-ui.input name="password_confirmation" type="password" required autocomplete="new-password" />
            </div>

            <x-ui.button class="w-full">Crear cuenta</x-ui.button>
        </form>

        <p class="mt-6 text-sm text-ink-muted">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-ink">Inicia sesión</a>
        </p>
    </div>
@endsection
