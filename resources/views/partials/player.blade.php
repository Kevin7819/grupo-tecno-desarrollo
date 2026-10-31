@php
    $configured = $stream->isConfigured();
@endphp

<section id="reproductor"
         data-player
         data-configured="{{ $configured ? 'true' : 'false' }}"
         @if ($configured) data-stream-url="{{ $stream->url }}" data-stream-format="{{ $stream->format->value }}" @endif
         aria-labelledby="reproductor-titulo"
         class="min-w-0 scroll-mt-24 rounded-2xl border border-surface-border bg-surface-card p-5 shadow-lg shadow-black/20 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0">
            <h2 id="reproductor-titulo" class="text-lg font-semibold">Reproductor</h2>
            <p class="mt-1 text-[0.6875rem] font-semibold uppercase tracking-[0.18em] text-ink-muted">Emisora</p>
            <p class="mt-0.5 break-words text-base font-medium text-ink">
                {{ $stream->name ?? 'Transmisión sin nombre configurado' }}
            </p>
        </div>

        @if ($configured)
            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-brand/40 bg-brand-soft px-3 py-1 text-xs font-semibold uppercase tracking-widest text-brand">
                <span class="live-dot" aria-hidden="true"></span>
                EN VIVO
            </span>
        @endif
    </div>

    @if ($configured)
        <audio data-player-audio preload="none" class="hidden"></audio>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
            <button type="button"
                    data-player-toggle
                    class="action-primary w-full sm:w-auto">
                <svg data-player-icon="play" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
                <svg data-player-icon="pause" class="hidden size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                </svg>
                <svg data-player-icon="retry" class="hidden size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span data-player-label>Escuchar</span>
            </button>

            <p data-player-status
               role="status"
               aria-live="polite"
               aria-atomic="true"
               class="min-h-6 min-w-0 break-words text-sm text-ink-muted">Detenido</p>
        </div>

        <div class="mt-5 flex items-center justify-between gap-4 rounded-xl border border-surface-border bg-surface-base/40 p-3 sm:p-4">
            <div class="player-visualizer" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="text-right text-[0.6875rem] leading-snug text-ink-muted sm:text-xs">
                Animación decorativa.<br class="hidden sm:block">
                No mide la señal de audio.
            </p>
        </div>

        <div class="mt-5 rounded-xl border border-surface-border bg-surface-base/40 p-3 sm:p-4">
            <div class="flex items-center gap-3">
                <button type="button"
                        data-player-mute
                        aria-label="Silenciar"
                        aria-pressed="false"
                        title="Silenciar"
                        class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg border border-surface-border bg-surface-card text-ink transition hover:border-brand hover:text-brand">
                    <svg data-volume-icon="on" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7M18 6a8 8 0 0 1 0 12" />
                    </svg>
                    <svg data-volume-icon="off" class="hidden size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16 9 5 5m0-5-5 5" />
                    </svg>
                </button>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <label for="player-volume" class="text-xs font-medium text-ink-muted">Volumen</label>
                        <output for="player-volume" data-player-volume-value class="text-xs font-semibold tabular-nums text-ink" aria-hidden="true">80 %</output>
                    </div>

                    <input id="player-volume"
                           data-player-volume
                           type="range"
                           min="0"
                           max="100"
                           step="1"
                           value="80"
                           aria-describedby="player-volume-help"
                           class="volume-range mt-1">

                    <p id="player-volume-help" class="sr-only">En algunos dispositivos móviles el volumen se controla con los botones físicos del teléfono.</p>
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-2">
            <button type="button" data-player-share class="action-secondary min-h-11 px-4 py-2">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314M15.75 6a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0 16.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0-9a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" />
                </svg>
                Compartir
            </button>
            <p data-player-share-status role="status" aria-live="polite" class="min-w-0 text-xs text-ink-muted"></p>
        </div>

        <div data-share-fallback hidden class="mt-2">
            <label for="share-url" class="text-xs font-medium text-ink-muted">Copiá este enlace para compartir</label>
            <input id="share-url"
                   data-share-url
                   type="text"
                   readonly
                   value="{{ url('/') }}"
                   class="mt-1 w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-xs text-ink">
        </div>

        <p class="mt-4 text-xs text-ink-muted">
            El audio se reproduce directamente desde el proveedor de transmisión externo. La radio no aloja ni procesa el audio.
        </p>
    @else
        <p data-player-status role="status" aria-live="polite" aria-atomic="true" class="mt-6 text-sm text-ink-muted">Transmisión no configurada</p>
        <p class="mt-2 text-xs text-ink-muted">La emisora todavía no tiene una URL de transmisión configurada.</p>
    @endif
</section>
