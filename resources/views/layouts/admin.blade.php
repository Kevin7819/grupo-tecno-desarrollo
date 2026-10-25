<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel administrativo — Grupo Tecno Desarrollo')</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('Logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface-base text-ink">
    <a href="#contenido"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-surface-base">
        Saltar al contenido
    </a>

    <header class="border-b border-surface-border">
        <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('Logo.png') }}" alt="" class="h-8 w-auto">
                <span class="text-sm font-semibold tracking-wide">Panel administrativo</span>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <span class="text-ink-muted">{{ auth('admin')->user()->email }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <x-ui.button variant="secondary" class="px-3 py-2">Cerrar sesión</x-ui.button>
                </form>
            </div>
        </div>
    </header>

    <main id="contenido" class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</body>
</html>
