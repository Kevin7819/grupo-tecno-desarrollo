@php
    $configured = $stream->isConfigured();
    $stationName = $stream->name ?? 'Transmisión en vivo';
@endphp

@if ($configured)
    <div data-mini-player
         role="region"
         aria-label="Reproductor compacto"
         class="mini-player"
         hidden>
        <div class="mx-auto flex w-full max-w-6xl items-center gap-2 px-3 py-2 sm:gap-3 sm:px-6 lg:px-8">
            <span class="live-dot hidden shrink-0 sm:inline-block" aria-hidden="true"></span>

            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-ink">{{ $stationName }}</p>
                <p data-mini-state class="truncate text-xs text-ink-muted">Detenido</p>
            </div>

            <button type="button"
                    data-mini-toggle
                    aria-label="Reproducir"
                    class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg bg-brand text-surface-base transition-colors hover:bg-brand-strong">
                <svg data-mini-icon="play" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
                <svg data-mini-icon="pause" class="hidden size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                </svg>
                <svg data-mini-icon="retry" class="hidden size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </button>

            <button type="button"
                    data-mini-mute
                    aria-label="Silenciar"
                    aria-pressed="false"
                    title="Silenciar"
                    class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg border border-surface-border bg-surface-card text-ink transition-colors hover:border-brand hover:text-brand">
                <svg data-mini-volume-icon="on" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7M18 6a8 8 0 0 1 0 12" />
                </svg>
                <svg data-mini-volume-icon="off" class="hidden size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16 9 5 5m0-5-5 5" />
                </svg>
            </button>

            <a href="#reproductor"
               class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg border border-surface-border bg-surface-card text-ink transition-colors hover:border-brand hover:text-brand"
               aria-label="Ir al reproductor completo">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5V4.5m0 0-5.25 5.25M12 4.5l5.25 5.25" />
                </svg>
            </a>
        </div>
    </div>
@endif
