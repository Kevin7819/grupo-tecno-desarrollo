@extends('layouts.plain')

@section('title', 'Iniciar sesión — Grupo Tecno Desarrollo')

@section('content')
    <div class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-lg shadow-black/20 sm:p-8">
        <h1 class="text-2xl font-semibold">Iniciar sesión</h1>
        <p class="mt-2 text-sm text-ink-muted">Accede a tu cuenta de oyente.</p>

        @if (session('status'))
            <x-ui.alert class="mt-6">{{ session('status') }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
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

            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded border-surface-border bg-surface-base">
                Recordarme
            </label>

            <x-ui.button class="w-full">Entrar</x-ui.button>
        </form>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-sm">
            <a href="{{ route('password.request') }}" class="text-ink-muted hover:text-ink">¿Olvidaste tu contraseña?</a>
            <a href="{{ route('register') }}" class="font-semibold text-brand hover:text-ink">Crear cuenta</a>
        </div>
    </div>
@endsection
