const HLS_MIME = 'application/vnd.apple.mpegurl';

const MESSAGES = {
    idle: 'Detenido',
    loading: 'Conectando…',
    playing: 'En vivo',
    paused: 'En pausa',
    reconnecting: 'Reconectando…',
    error: 'No se pudo conectar con la transmisión. Intenta de nuevo.',
    unsupportedHls: 'Este navegador no puede reproducir HLS sin un reproductor adicional.',
};

const LABELS = {
    play: 'Escuchar',
    pause: 'Pausar',
    retry: 'Reintentar',
};

function setState(player, state, message) {
    player.dataset.playerState = state;

    const status = player.querySelector('[data-player-status]');
    const label = player.querySelector('[data-player-label]');

    if (status && typeof message === 'string') {
        status.textContent = message;
    }

    if (label) {
        label.textContent =
            state === 'playing'
                ? LABELS.pause
                : state === 'error'
                  ? LABELS.retry
                  : LABELS.play;
    }
}

function initPlayer(player) {
    if (player.dataset.configured !== 'true') {
        return;
    }

    const audio = player.querySelector('[data-player-audio]');
    const toggle = player.querySelector('[data-player-toggle]');

    if (!audio || !toggle) {
        return;
    }

    const url = player.dataset.streamUrl ?? '';
    const format = (player.dataset.streamFormat ?? 'mp3').toLowerCase();

    if (url === '') {
        setState(player, 'error', MESSAGES.error);
        toggle.disabled = true;

        return;
    }

    if (format === 'hls' && audio.canPlayType(HLS_MIME) === '') {
        setState(player, 'error', MESSAGES.unsupportedHls);
        toggle.disabled = true;

        return;
    }

    let loaded = false;

    audio.addEventListener('loadstart', () => setState(player, 'loading', MESSAGES.loading));
    audio.addEventListener('waiting', () => setState(player, 'loading', MESSAGES.loading));
    audio.addEventListener('playing', () => setState(player, 'playing', MESSAGES.playing));
    audio.addEventListener('pause', () => {
        if (!audio.ended) {
            setState(player, 'paused', MESSAGES.paused);
        }
    });
    audio.addEventListener('stalled', () => setState(player, 'loading', MESSAGES.reconnecting));
    audio.addEventListener('error', () => setState(player, 'error', MESSAGES.error));

    toggle.addEventListener('click', async () => {
        if (!loaded) {
            audio.src = url;
            loaded = true;
        }

        if (!audio.paused) {
            audio.pause();

            return;
        }

        try {
            setState(player, 'loading', MESSAGES.loading);
            await audio.play();
        } catch {
            setState(player, 'error', MESSAGES.error);
        }
    });

    setState(player, 'idle', MESSAGES.idle);
}

document.querySelectorAll('[data-player]').forEach(initPlayer);
