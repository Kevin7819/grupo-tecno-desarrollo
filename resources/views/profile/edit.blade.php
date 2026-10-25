@extends('layouts.app')

@section('title', 'Mi cuenta — Grupo Tecno Desarrollo')

@section('content')
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold">Mi cuenta</h1>
        <p class="mt-2 text-ink-muted">Administra tus datos de acceso.</p>

        @if (session('status'))
            <x-ui.alert class="mt-6">{{ session('status') }}</x-ui.alert>
        @endif

        <section class="mt-8 rounded-2xl border border-surface-border bg-surface-card p-6">
            <h2 class="text-lg font-semibold">Datos personales</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-4 space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium">Nombre</label>
                    <x-ui.input name="name" :value="old('name', $user->name)" required autocomplete="name" />
                    @error('name')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                    <x-ui.input name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
                    @error('email')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button>Guardar cambios</x-ui.button>
            </form>
        </section>

        <section class="mt-6 rounded-2xl border border-surface-border bg-surface-card p-6">
            <h2 class="text-lg font-semibold">Cambiar contraseña</h2>

            <form method="POST" action="{{ route('profile.password') }}" class="mt-4 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium">Contraseña actual</label>
                    <x-ui.input name="current_password" type="password" required autocomplete="current-password" />
                    @error('current_password')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium">Nueva contraseña</label>
                    <x-ui.input name="password" type="password" required autocomplete="new-password" />
                    @error('password')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium">Repite la nueva contraseña</label>
                    <x-ui.input name="password_confirmation" type="password" required autocomplete="new-password" />
                </div>

                <x-ui.button>Actualizar contraseña</x-ui.button>
            </form>
        </section>
    </div>
@endsection
