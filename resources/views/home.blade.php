@extends('layouts.app')

@section('title', 'Grupo Tecno Desarrollo — Radio por Internet')

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 pb-16 pt-10 sm:px-6 sm:pt-14 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-brand">Radio por Internet</p>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">Grupo Tecno Desarrollo</h1>

                <p class="mt-4 max-w-xl text-lg text-ink-muted">
                    @if ($stream->isConfigured())
                        Escucha la transmisión en vivo desde tu navegador.
                    @else
                        El reproductor estará disponible cuando se configure el proveedor de audio.
                    @endif
                </p>

                @if (session('status'))
                    <x-ui.alert class="mt-6">{{ session('status') }}</x-ui.alert>
                @endif

                @guest('web')
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-surface-base transition hover:bg-brand-strong">
                            Crear cuenta
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-lg border border-surface-border bg-surface-card px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-brand hover:text-brand">
                            Iniciar sesión
                        </a>
                    </div>
                    <p class="mt-3 text-sm text-ink-muted">Crea una cuenta para acceder a tu área personal.</p>
                @endguest

                @auth('web')
                    <p class="mt-8 text-sm text-ink-muted">
                        Sesión iniciada como <span class="font-semibold text-ink">{{ auth('web')->user()->name }}</span>.
                    </p>
                @endauth
            </div>

            <div>
                @include('partials.player')
            </div>
        </div>
    </div>
@endsection
