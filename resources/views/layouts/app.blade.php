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

    @php($streamConfigured = $stream->isConfigured())

    <header class="sticky top-0 z-30 border-b border-surface-border bg-surface-base/85 backdrop-blur">
        <div class="mx-auto flex w-full max-w-6xl items-center gap-3 px-3 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5 rounded-lg">
                <img src="{{ asset('Logo-mark.png') }}"
                     alt=""
                     width="270"
                     height="325"
                     class="h-9 w-auto shrink-0 rounded-lg">
                <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold tracking-wide text-ink">Grupo Tecno Desarrollo</span>
                    <span class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="text-[0.625rem] font-medium uppercase tracking-[0.18em] text-ink-muted">Radio por Internet</span>
                        @if ($streamConfigured)
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-brand/40 bg-brand-soft px-2 py-0.5 text-[0.625rem] font-semibold uppercase tracking-widest text-brand">
                                <span class="live-dot" aria-hidden="true"></span>
                                EN VIVO
                            </span>
                        @endif
                    </span>
                </span>
            </a>

            <nav aria-label="Secciones" class="ml-auto hidden items-center gap-1 lg:flex">
                <a href="{{ route('home') }}#inicio" class="nav-link">Inicio</a>
                <a href="{{ route('home') }}#nosotros" class="nav-link">Nosotros</a>
                <a href="{{ route('home') }}#programacion" class="nav-link">Programación</a>
                <a href="{{ route('home') }}#contacto" class="nav-link">Contacto</a>
            </nav>

            <div class="ml-auto flex shrink-0 items-center gap-1.5 lg:ml-2">
                <a href="{{ route('home') }}#reproductor"
                   data-player-start
                   class="action-primary hidden px-4 py-2.5 sm:inline-flex">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    Escuchar en vivo
                </a>

                <div class="hidden items-center gap-1 xl:flex">
                    @auth('web')
                        <a href="{{ route('profile.edit') }}" class="action-ghost">Mi cuenta</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui.button variant="ghost" class="min-h-11 px-3">Cerrar sesión</x-ui.button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="action-ghost">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="action-ghost text-ink">Crear cuenta</a>
                    @endauth
                </div>

                <button type="button"
                        data-menu-toggle
                        aria-expanded="false"
                        aria-controls="menu-movil"
                        class="inline-flex size-11 items-center justify-center rounded-lg border border-surface-border bg-surface-card text-ink transition-colors hover:border-brand hover:text-brand lg:hidden">
                    <span class="sr-only" data-menu-label>Abrir menú</span>
                    <svg data-menu-icon="open" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                    <svg data-menu-icon="close" class="hidden size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="menu-movil" class="border-t border-surface-border bg-surface-card lg:hidden" hidden>
            <div class="mx-auto w-full max-w-6xl px-4 py-4 sm:px-6">
                <nav aria-label="Secciones" class="grid gap-0.5">
                    <a href="{{ route('home') }}#inicio" class="nav-link min-h-12 px-2">Inicio</a>
                    <a href="{{ route('home') }}#nosotros" class="nav-link min-h-12 px-2">Nosotros</a>
                    <a href="{{ route('home') }}#programacion" class="nav-link min-h-12 px-2">Programación</a>
                    <a href="{{ route('home') }}#contacto" class="nav-link min-h-12 px-2">Contacto</a>
                </nav>

                <a href="{{ route('home') }}#reproductor" data-player-start class="action-primary mt-3 w-full">
                    Escuchar en vivo
                </a>

                <div class="mt-4 grid gap-0.5 border-t border-surface-border pt-3 text-sm">
                    @auth('web')
                        <a href="{{ route('profile.edit') }}" class="nav-link min-h-12 px-2">Mi cuenta</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui.button variant="ghost" class="min-h-12 w-full justify-start px-2">Cerrar sesión</x-ui.button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link min-h-12 px-2">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="nav-link min-h-12 px-2 text-ink">Crear cuenta</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main id="contenido" class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-surface-border bg-surface-card/40">
        <div class="mx-auto grid w-full max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr] lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('Logo-mark.png') }}"
                         alt=""
                         width="270"
                         height="325"
                         class="h-10 w-auto rounded-lg">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-ink">Grupo Tecno Desarrollo</p>
                        <p class="text-xs uppercase tracking-[0.18em] text-ink-muted">Radio por Internet</p>
                    </div>
                </div>
                <p class="mt-4 max-w-sm text-sm text-ink-muted">
                    Sitio en construcción. La programación, los canales oficiales y los textos legales se publicarán cuando estén confirmados.
                </p>
            </div>

            <nav aria-label="Navegación del sitio">
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-ink-muted">Navegación</h2>
                <ul class="mt-3 grid gap-1.5 text-sm">
                    <li><a href="{{ route('home') }}#inicio" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Inicio</a></li>
                    <li><a href="{{ route('home') }}#nosotros" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Nosotros</a></li>
                    <li><a href="{{ route('home') }}#programacion" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Programación</a></li>
                    <li><a href="{{ route('home') }}#contacto" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Contacto</a></li>
                </ul>
            </nav>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-ink-muted">Cuenta</h2>
                <ul class="mt-3 grid gap-1.5 text-sm">
                    @auth('web')
                        <li><a href="{{ route('profile.edit') }}" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Mi cuenta</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Iniciar sesión</a></li>
                        <li><a href="{{ route('register') }}" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Crear cuenta</a></li>
                    @endauth
                    <li><a href="{{ route('admin.login') }}" class="inline-flex min-h-9 items-center text-ink-muted transition-colors hover:text-ink">Acceso administradores</a></li>
                </ul>

                @if ($contactChannels->hasEmail())
                    <p class="mt-4 text-sm">
                        <a href="{{ $contactChannels->mailto() }}" class="font-medium text-ink underline decoration-brand/60 underline-offset-4 hover:text-brand">
                            {{ $contactChannels->email }}
                        </a>
                    </p>
                @endif
            </div>
        </div>

        <div class="border-t border-surface-border">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-1 px-4 py-5 text-xs text-ink-muted sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>© {{ now()->year }} Grupo Tecno Desarrollo</p>
                <p>El audio se reproduce desde el proveedor de transmisión externo.</p>
            </div>
        </div>
    </footer>

    @include('partials.mini-player')
</body>
</html>
