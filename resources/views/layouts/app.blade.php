<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Grupo Tecno Desarrollo')</title>
    <meta name="description" content="@yield('description', 'Radio por Internet de Grupo Tecno Desarrollo.')">

    @include('partials.favicon')

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Grupo Tecno Desarrollo">
    <meta property="og:title" content="@yield('title', 'Grupo Tecno Desarrollo')">
    <meta property="og:description" content="@yield('description', 'Radio por Internet de Grupo Tecno Desarrollo.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="es_ES">
    <meta property="og:image" content="{{ asset('Logo.jpg') }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">
    <meta property="og:image:alt" content="Logotipo de Grupo Tecno Desarrollo">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', 'Grupo Tecno Desarrollo')">
    <meta name="twitter:description" content="@yield('description', 'Radio por Internet de Grupo Tecno Desarrollo.')">
    <meta name="twitter:image" content="{{ asset('Logo.jpg') }}">
    <meta name="twitter:image:alt" content="Logotipo de Grupo Tecno Desarrollo">

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
                <img src="{{ asset('Logo-mark.png') }}" alt="" width="270" height="325" class="h-9 w-auto rounded-lg">
                <span class="text-sm font-semibold tracking-wide text-ink">Grupo Tecno Desarrollo</span>
            </a>

            <nav aria-label="Cuenta" class="flex items-center gap-2 text-sm">
                @auth('web')
                    <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2 text-ink-muted hover:text-ink">Mi cuenta</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button variant="secondary" class="px-3 py-2">Cerrar sesión</x-ui.button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-ink-muted hover:text-ink">Iniciar sesión</a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-brand px-3 py-2 font-semibold text-surface-base transition hover:bg-brand-strong">
                        Crear cuenta
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="contenido" class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-surface-border">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-ink-muted sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('Logo-mark.png') }}" alt="" width="270" height="325" class="h-6 w-auto rounded-md">
                <p>Grupo Tecno Desarrollo — Radio por Internet</p>
            </div>
            <p>{{ now()->year }}</p>
        </div>
    </footer>
</body>
</html>
