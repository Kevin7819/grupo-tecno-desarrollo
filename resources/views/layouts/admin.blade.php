<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel administrativo — Grupo Tecno Desarrollo')</title>
    <meta name="robots" content="noindex, nofollow">
    @include('partials.favicon')
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
                <img src="{{ asset('Logo-mark.png') }}" alt="" width="270" height="325" class="h-8 w-auto rounded-lg">
                <div class="min-w-0">
                    <span class="block truncate text-sm font-semibold tracking-wide">
                        {{ $stationSettings->stationName ?? 'Grupo Tecno Desarrollo' }}
                    </span>
                    <span class="block text-xs text-ink-muted">Panel administrativo</span>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('home') }}" class="action-ghost">Ver sitio</a>
                <span class="hidden text-ink-muted sm:inline">{{ auth('admin')->user()->email }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <x-ui.button variant="secondary" class="px-3 py-2">Cerrar sesión</x-ui.button>
                </form>
            </div>
        </div>

        <nav aria-label="Secciones del panel" class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <ul class="flex flex-wrap gap-1 pb-3">
                @foreach ([
                    ['admin.dashboard', 'admin.dashboard', 'Panel'],
                    ['admin.settings.edit', 'admin.settings.*', 'Emisora'],
                    ['admin.programs.index', 'admin.programs.*', 'Programación'],
                    ['admin.hosts.index', 'admin.hosts.*', 'Locutores'],
                ] as [$route, $pattern, $label])
                    @php($active = request()->routeIs($pattern))
                    <li>
                        <a href="{{ route($route) }}"
                           @if ($active) aria-current="page" @endif
                           @class([
                               'inline-flex min-h-11 items-center rounded-lg px-3 text-sm font-medium transition-colors',
                               'bg-brand-soft text-brand' => $active,
                               'text-ink-muted hover:bg-surface-border/50 hover:text-ink' => ! $active,
                           ])>
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </header>

    <main id="contenido" class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <x-ui.alert class="mb-6">{{ session('status') }}</x-ui.alert>
        @endif

        @yield('content')
    </main>
</body>
</html>
