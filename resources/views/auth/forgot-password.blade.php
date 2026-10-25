@extends('layouts.plain')

@section('title', 'Recuperar contraseña — Grupo Tecno Desarrollo')

@section('content')
    <div class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-lg shadow-black/20 sm:p-8">
        <h1 class="text-2xl font-semibold">Recuperar contraseña</h1>
        <p class="mt-2 text-sm text-ink-muted">
            Escribe el correo de tu cuenta y te enviaremos un enlace para restablecer la contraseña.
        </p>

        @if (session('status'))
            <x-ui.alert class="mt-6">{{ session('status') }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                <x-ui.input name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <x-ui.button class="w-full">Enviar enlace</x-ui.button>
        </form>

        <p class="mt-6 text-sm text-ink-muted">
            <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-ink">Volver a iniciar sesión</a>
        </p>
    </div>
@endsection
