<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Grupo Tecno Desarrollo')</title>
    <meta name="description" content="@yield('description', 'Radio por Internet de Grupo Tecno Desarrollo.')">
    <link rel="icon" href="{{ asset('Logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface-base text-ink">
    <a href="#contenido"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-surface-base">
        Saltar al contenido
    </a>

    <header class="border-b border-surface-border">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg">
                <img src="{{ asset('Logo.png') }}" alt="" class="h-8 w-auto">
                <span class="text-sm font-semibold tracking-wide text-ink">Grupo Tecno Desarrollo</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm text-ink-muted hover:text-ink">Volver al inicio</a>
        </div>
    </header>

    <main id="contenido" class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6">
        <div class="w-full max-w-md">
            @yield('content')
        </div>
    </main>
</body>
</html>
