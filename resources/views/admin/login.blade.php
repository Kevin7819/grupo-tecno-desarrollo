@extends('layouts.plain')

@section('title', 'Acceso administrativo — Grupo Tecno Desarrollo')

@section('content')
    <div class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-lg shadow-black/20 sm:p-8">
        <h1 class="text-2xl font-semibold">Acceso administrativo</h1>
        <p class="mt-2 text-sm text-ink-muted">Este acceso es exclusivo para administradores de la emisora.</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                <x-ui.input name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Contraseña</label>
                <x-ui.input name="password" type="password" required autocomplete="current-password" />
                @error('password')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <x-ui.button class="w-full">Entrar</x-ui.button>
        </form>
    </div>
@endsection
