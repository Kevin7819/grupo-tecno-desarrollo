@extends('layouts.app')

@section('title', 'Grupo Tecno Desarrollo — Radio por Internet')
@section('description', 'Radio por Internet de Grupo Tecno Desarrollo. Escuchá la transmisión desde el navegador.')

@section('content')
    {{-- A. Hero: mensaje de bienvenida y reproductor protagonista --}}
    <section id="inicio" class="relative scroll-mt-24 overflow-hidden border-b border-surface-border">
        <div aria-hidden="true"
             class="pointer-events-none absolute inset-0 bg-[radial-gradient(80%_60%_at_15%_0%,rgba(255,85,40,0.14),transparent_65%)]"></div>

        <div class="relative mx-auto grid w-full max-w-6xl gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-12 lg:px-8">
            <div class="min-w-0">
                <p class="section-eyebrow">Radio por Internet</p>

                <h1 class="mt-4 text-balance text-4xl font-semibold tracking-tight text-ink sm:text-5xl lg:text-6xl">
                    Tu música, tu momento
                </h1>

                <p class="mt-5 max-w-xl text-base text-ink-muted sm:text-lg">
                    Grupo Tecno Desarrollo es un proyecto de radio por Internet. Abrí el reproductor y escuchá
                    desde el navegador, en la computadora o en el teléfono. La programación definitiva se irá
                    publicando a medida que esté aprobada.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="#reproductor" data-player-start class="action-primary">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                        </svg>
                        Escuchar ahora
                    </a>

                    <a href="#nosotros" class="action-secondary">
                        Conocer la emisora
                    </a>
                </div>

                <ul class="mt-8 flex flex-wrap gap-2">
                    <li class="chip">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                        </svg>
                        Audio en el navegador
                    </li>

                    @if ($stream->isConfigured())
                        <li class="chip">
                            <span class="live-dot" aria-hidden="true"></span>
                            Estado:
                            <span data-player-state-text aria-hidden="true">Detenido</span>
                            <span class="sr-only">El estado de la transmisión se anuncia en el reproductor.</span>
                        </li>
                    @else
                        <li class="chip">Transmisión no configurada</li>
                    @endif
                </ul>

                @if (session('status'))
                    <x-ui.alert class="mt-6">{{ session('status') }}</x-ui.alert>
                @endif
            </div>

            <div class="min-w-0">
                @include('partials.player')
            </div>
        </div>
    </section>

    {{-- D. Sobre la emisora --}}
    <section id="nosotros" class="scroll-mt-24 border-b border-surface-border py-14 sm:py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="section-eyebrow">Nosotros</p>
                <h2 class="section-title">Sobre la emisora</h2>
                <p class="section-lead">
                    Grupo Tecno Desarrollo es un proyecto de radio por Internet en construcción. La idea es
                    simple: una señal de audio que se escucha desde el navegador, sin instalar aplicaciones.
                    La identidad, la programación y los servicios definitivos se publicarán cuando estén aprobados.
                </p>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article class="surface-card">
                    <span class="inline-flex size-10 items-center justify-center rounded-xl border border-brand/30 bg-brand-soft text-brand">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h2.25m3 0h1.5m3 0h1.5m3 0H21M3 7.5h18M3 16.5h18" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-ink">Sonido digital</h3>
                    <p class="mt-2 text-sm text-ink-muted">
                        El audio llega por Internet desde un proveedor de transmisión externo. Este sitio no
                        aloja ni procesa el audio: solo lo reproduce.
                    </p>
                </article>

                <article class="surface-card">
                    <span class="inline-flex size-10 items-center justify-center rounded-xl border border-brand/30 bg-brand-soft text-brand">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-ink">Experiencia de escucha</h3>
                    <p class="mt-2 text-sm text-ink-muted">
                        Un reproductor con controles de teclado, volumen propio y una barra compacta para seguir
                        escuchando mientras recorrés el sitio. La reproducción empieza solo cuando la activás.
                    </p>
                </article>

                <article class="surface-card">
                    <span class="inline-flex size-10 items-center justify-center rounded-xl border border-brand/30 bg-brand-soft text-brand">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-ink">Evolución y mejora continua</h3>
                    <p class="mt-2 text-sm text-ink-muted">
                        El proyecto está en preparación. La parrilla, los canales oficiales y los textos legales
                        se irán sumando a medida que estén confirmados, sin publicar información provisoria como
                        si fuera definitiva.
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- E. Programación --}}
    <section id="programacion" class="scroll-mt-24 border-b border-surface-border py-14 sm:py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="section-eyebrow">Programación</p>
                <h2 class="section-title">Bloques y propuestas</h2>
                <p class="section-lead">
                    Todavía no hay una parrilla confirmada. Estos bloques son ejemplos de estructura y cada uno
                    indica su estado real.
                </p>
            </div>

            <div class="mt-6 rounded-2xl border border-surface-border bg-surface-card/60 p-4 sm:p-5">
                <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-ink-muted">Estados de la parrilla</h3>
                <ul class="mt-3 grid gap-3 text-sm text-ink-muted sm:grid-cols-3">
                    <li>
                        <span class="inline-flex items-center gap-2 rounded-full border border-success/40 bg-success/10 px-2.5 py-0.5 text-xs font-semibold text-success">
                            Disponible actualmente
                        </span>
                        <p class="mt-2 text-xs">Bloque confirmado y en emisión. Hoy no hay ninguno en este estado.</p>
                    </li>
                    <li>
                        <span class="inline-flex items-center gap-2 rounded-full border border-warning/40 bg-warning/10 px-2.5 py-0.5 text-xs font-semibold text-warning">
                            En preparación
                        </span>
                        <p class="mt-2 text-xs">Bloque definido, pendiente de horario y contenido final.</p>
                    </li>
                    <li>
                        <span class="inline-flex items-center gap-2 rounded-full border border-info/40 bg-info/10 px-2.5 py-0.5 text-xs font-semibold text-info">
                            Propuesta futura
                        </span>
                        <p class="mt-2 text-xs">Idea en evaluación. No tiene fecha ni confirmación.</p>
                    </li>
                </ul>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $blocks = [
                        ['name' => 'Bloque matinal', 'state' => 'preparing', 'slot' => 'Franja de la mañana', 'text' => 'Espacio reservado para la programación de la mañana. Sin horario confirmado.'],
                        ['name' => 'Bloque de la tarde', 'state' => 'preparing', 'slot' => 'Franja de la tarde', 'text' => 'Espacio reservado para la programación de la tarde. Sin horario confirmado.'],
                        ['name' => 'Selección de autor', 'state' => 'proposal', 'slot' => 'Bloque musical', 'text' => 'Propuesta de selección musical a cargo de la emisora. Aún no confirmada.'],
                        ['name' => 'Sesión nocturna', 'state' => 'proposal', 'slot' => 'Franja nocturna', 'text' => 'Propuesta de bloque nocturno. No tiene fecha ni contenido definido.'],
                        ['name' => 'Novedades', 'state' => 'proposal', 'slot' => 'Bloque musical', 'text' => 'Propuesta para compartir lanzamientos. Sin calendario definido.'],
                        ['name' => 'Archivo sonoro', 'state' => 'proposal', 'slot' => 'Bloque musical', 'text' => 'Propuesta de material de archivo. Sujeta a disponibilidad y permisos.'],
                    ];
                @endphp

                @foreach ($blocks as $block)
                    <article class="surface-card">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[0.6875rem] font-semibold uppercase tracking-[0.18em] text-ink-muted">{{ $block['slot'] }}</p>
                                <h3 class="mt-2 text-base font-semibold text-ink">{{ $block['name'] }}</h3>
                            </div>

                            @if ($block['state'] === 'preparing')
                                <span class="shrink-0 rounded-full border border-warning/40 bg-warning/10 px-2.5 py-0.5 text-[0.6875rem] font-semibold text-warning">
                                    En preparación
                                </span>
                            @else
                                <span class="shrink-0 rounded-full border border-info/40 bg-info/10 px-2.5 py-0.5 text-[0.6875rem] font-semibold text-info">
                                    Propuesta futura
                                </span>
                            @endif
                        </div>
                        <p class="mt-3 text-sm text-ink-muted">{{ $block['text'] }}</p>
                    </article>
                @endforeach
            </div>

            <p class="mt-6 text-xs text-ink-muted">
                Horarios, locutores, programas y eventos no están confirmados. Esta sección no describe
                emisiones reales.
            </p>
        </div>
    </section>

    {{-- F. Galería visual --}}
    <section id="galeria" class="scroll-mt-24 border-b border-surface-border py-14 sm:py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="section-eyebrow">Galería</p>
                <h2 class="section-title">Identidad y entorno sonoro</h2>
                <p class="section-lead">
                    Composiciones gráficas construidas con los recursos de marca del proyecto. Todavía no hay
                    fotografías de cabina ni de producción.
                </p>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <figure class="overflow-hidden rounded-2xl border border-surface-border bg-surface-card">
                    <div class="gallery-art gallery-art--mark flex items-center justify-center">
                        <img src="{{ asset('Logo.jpg') }}"
                             alt="Logotipo de Grupo Tecno Desarrollo"
                             width="1024"
                             height="1024"
                             loading="lazy"
                             decoding="async"
                             class="h-[78%] w-auto object-contain">
                    </div>
                    <figcaption class="flex items-center justify-between gap-2 px-4 py-3">
                        <span class="text-sm text-ink">Identidad</span>
                        <span class="text-[0.6875rem] font-semibold uppercase tracking-widest text-ink-muted">Marca</span>
                    </figcaption>
                </figure>

                <figure class="overflow-hidden rounded-2xl border border-surface-border bg-surface-card">
                    <div class="gallery-art gallery-art--booth flex items-end justify-center gap-1.5 p-8">
                        <span class="h-10 w-2 rounded-full bg-brand/70"></span>
                        <span class="h-16 w-2 rounded-full bg-brand"></span>
                        <span class="h-8 w-2 rounded-full bg-brand/50"></span>
                        <span class="h-20 w-2 rounded-full bg-brand/80"></span>
                        <span class="h-12 w-2 rounded-full bg-brand/60"></span>
                    </div>
                    <figcaption class="flex items-center justify-between gap-2 px-4 py-3">
                        <span class="text-sm text-ink">Ecualizador</span>
                        <span class="text-[0.6875rem] font-semibold uppercase tracking-widest text-ink-muted">Ilustración</span>
                    </figcaption>
                </figure>

                <figure class="overflow-hidden rounded-2xl border border-surface-border bg-surface-card">
                    <div class="gallery-art gallery-art--signal">
                        <span class="gallery-art__grid"></span>
                        <span class="absolute inset-0 bg-[radial-gradient(60%_60%_at_50%_50%,rgba(255,85,40,0.35),transparent_70%)]"></span>
                    </div>
                    <figcaption class="flex items-center justify-between gap-2 px-4 py-3">
                        <span class="text-sm text-ink">Señal</span>
                        <span class="text-[0.6875rem] font-semibold uppercase tracking-widest text-ink-muted">Ilustración</span>
                    </figcaption>
                </figure>

                <figure class="overflow-hidden rounded-2xl border border-surface-border bg-surface-card">
                    <div class="gallery-art gallery-art--wave">
                        <span class="gallery-art__grid"></span>
                    </div>
                    <figcaption class="flex items-center justify-between gap-2 px-4 py-3">
                        <span class="text-sm text-ink">Cabina</span>
                        <span class="text-[0.6875rem] font-semibold uppercase tracking-widest text-ink-muted">Ilustración</span>
                    </figcaption>
                </figure>
            </div>

            <p class="mt-6 text-xs text-ink-muted">
                Las imágenes de esta sección son composiciones gráficas de marca. No son fotografías de
                instalaciones reales de Grupo Tecno Desarrollo.
            </p>
        </div>
    </section>

    {{-- G. Comunidad y contacto --}}
    <section id="contacto" class="scroll-mt-24 py-14 sm:py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="section-eyebrow">Comunidad</p>
                <h2 class="section-title">Comunidad y contacto</h2>
                <p class="section-lead">
                    Los canales oficiales se publicarán en esta sección cuando estén confirmados. Mientras
                    tanto, no hay cuentas ni direcciones de contacto públicas que mostrar.
                </p>
            </div>

            <div class="mt-10 grid gap-4 lg:grid-cols-3">
                <div class="surface-card">
                    <h3 class="text-base font-semibold text-ink">Redes sociales</h3>

                    @if ($channels->hasSocial())
                        <ul class="mt-4 grid gap-2">
                            @foreach ($channels->social as $label => $url)
                                <li>
                                    <a href="{{ $url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-surface-border bg-surface-base/40 px-3 text-sm font-medium text-ink transition-colors hover:border-brand hover:text-brand">
                                        {{ $label }}
                                        <span class="sr-only">(se abre en una pestaña nueva)</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="mt-4 rounded-xl border border-dashed border-surface-border bg-surface-base/40 p-4">
                            <p class="text-sm text-ink">Todavía no hay cuentas oficiales publicadas.</p>
                            <p class="mt-1 text-xs text-ink-muted">
                                Cuando la emisora confirme sus perfiles, aparecerán acá con su enlace correspondiente.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="surface-card">
                    <h3 class="text-base font-semibold text-ink">Contacto</h3>

                    @if ($channels->hasEmail())
                        <p class="mt-4 text-sm text-ink-muted">Escribinos por correo:</p>
                        <p class="mt-2">
                            <a href="{{ $channels->mailto() }}"
                               class="inline-flex min-h-11 items-center break-all font-medium text-ink underline decoration-brand/60 underline-offset-4 hover:text-brand">
                                {{ $channels->email }}
                            </a>
                        </p>
                    @else
                        <div class="mt-4 rounded-xl border border-dashed border-surface-border bg-surface-base/40 p-4">
                            <p class="text-sm text-ink">Todavía no hay un correo de contacto publicado.</p>
                            <p class="mt-1 text-xs text-ink-muted">
                                El correo se habilitará cuando la emisora lo confirme.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="surface-card">
                    <h3 class="text-base font-semibold text-ink">Tu cuenta</h3>
                    <p class="mt-4 text-sm text-ink-muted">
                        Podés crear una cuenta de oyente para acceder a tu área personal.
                    </p>

                    <div class="mt-4 flex flex-col gap-2">
                        @auth('web')
                            <a href="{{ route('profile.edit') }}" class="action-secondary">Mi cuenta</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-ui.button variant="secondary" class="w-full">Cerrar sesión</x-ui.button>
                            </form>
                        @else
                            <a href="{{ route('register') }}" class="action-primary">Crear cuenta</a>
                            <a href="{{ route('login') }}" class="action-secondary">Iniciar sesión</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
