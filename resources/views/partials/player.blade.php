@php
    $configured = $stream->isConfigured();
@endphp

<section id="reproductor"
         data-player
         data-configured="{{ $configured ? 'true' : 'false' }}"
         @if ($configured) data-stream-url="{{ $stream->url }}" data-stream-format="{{ $stream->format->value }}" @endif
         aria-labelledby="reproductor-titulo"
         class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-lg shadow-black/20">
    <div class="flex items-center justify-between gap-4">
        <h2 id="reproductor-titulo" class="text-lg font-semibold">Reproductor</h2>

        @if ($configured)
            <span class="inline-flex items-center gap-2 rounded-full border border-brand/40 bg-brand-soft px-3 py-1 text-xs font-semibold uppercase tracking-widest text-brand">
                <span class="live-dot" aria-hidden="true"></span>
                EN VIVO
            </span>
        @endif
    </div>

    @if ($stream->name)
        <p class="mt-4 text-sm text-ink-muted">{{ $stream->name }}</p>
    @endif

    @if ($configured)
        <audio data-player-audio preload="none" class="hidden"></audio>

        <div class="mt-6 flex flex-wrap items-center gap-4">
            <button type="button"
                    data-player-toggle
                    class="inline-flex items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-surface-base transition hover:bg-brand-strong disabled:cursor-not-allowed disabled:opacity-60">
                <span data-player-label>Escuchar</span>
            </button>
            <p data-player-status role="status" aria-live="polite" class="text-sm text-ink-muted">Detenido</p>
        </div>

        <p class="mt-4 text-xs text-ink-muted">El audio se reproduce directamente desde el proveedor de transmisión.</p>
    @else
        <p data-player-status role="status" class="mt-6 text-sm text-ink-muted">Transmisión no configurada</p>
        <p class="mt-2 text-xs text-ink-muted">La emisora todavía no tiene una URL de transmisión configurada.</p>
    @endif
</section>
