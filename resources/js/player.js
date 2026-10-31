const HLS_MIME = 'application/vnd.apple.mpegurl';

const VOLUME_KEY = 'grupo-tecno-radio.volume';
const MUTED_KEY = 'grupo-tecno-radio.muted';
const DEFAULT_VOLUME = 0.8;

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

const SHARE = {
    copied: 'Enlace copiado al portapapeles.',
    fallback: 'Copiá el enlace del campo de texto.',
};

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function readNumber(key, fallback) {
    try {
        const raw = window.localStorage.getItem(key);

        if (raw === null) {
            return fallback;
        }

        const value = Number(raw);

        return Number.isFinite(value) ? value : fallback;
    } catch {
        return fallback;
    }
}

function readBoolean(key, fallback) {
    try {
        const raw = window.localStorage.getItem(key);

        return raw === null ? fallback : raw === 'true';
    } catch {
        return fallback;
    }
}

function persist(key, value) {
    try {
        window.localStorage.setItem(key, String(value));
    } catch {
        // Storage can be unavailable in private mode; the in-memory value still works.
    }
}

function initPlayer(player) {
    if (player.dataset.configured !== 'true') {
        return;
    }

    const audio = player.querySelector('[data-player-audio]');
    const toggle = player.querySelector('[data-player-toggle]');
    const status = player.querySelector('[data-player-status]');
    const label = player.querySelector('[data-player-label]');
    const volume = player.querySelector('[data-player-volume]');
    const volumeValue = player.querySelector('[data-player-volume-value]');
    const mute = player.querySelector('[data-player-mute]');
    const share = player.querySelector('[data-player-share]');
    const shareStatus = player.querySelector('[data-player-share-status]');
    const shareFallback = player.querySelector('[data-share-fallback]');
    const shareInput = player.querySelector('[data-share-url]');
    const stateTexts = document.querySelectorAll('[data-player-state-text]');
    const starters = document.querySelectorAll('[data-player-start]');

    const mini = document.querySelector('[data-mini-player]');
    const miniToggle = mini ? mini.querySelector('[data-mini-toggle]') : null;
    const miniMute = mini ? mini.querySelector('[data-mini-mute]') : null;
    const miniState = mini ? mini.querySelector('[data-mini-state]') : null;

    if (!audio || !toggle) {
        return;
    }

    const url = (player.dataset.streamUrl ?? '').trim();
    const format = (player.dataset.streamFormat ?? 'mp3').toLowerCase();
    const loading = { loaded: false, hasPlayed: false };

    let storedVolume = clamp(readNumber(VOLUME_KEY, DEFAULT_VOLUME), 0, 1);
    let storedMuted = readBoolean(MUTED_KEY, false);
    let lastNonZero = storedVolume > 0 ? storedVolume : DEFAULT_VOLUME;

    function setState(state, message) {
        const playing = state === 'playing';
        const icon = state === 'error' ? 'retry' : playing ? 'pause' : 'play';
        const text = state === 'error' ? LABELS.retry : playing ? LABELS.pause : LABELS.play;

        player.dataset.playerState = state;

        if (mini) {
            mini.dataset.playerState = state;
        }

        if (status) {
            status.textContent = message;
        }

        if (label) {
            label.textContent = text;
        }

        player.querySelectorAll('[data-player-icon]').forEach((element) => {
            element.classList.toggle('hidden', element.dataset.playerIcon !== icon);
        });

        if (mini) {
            mini.querySelectorAll('[data-mini-icon]').forEach((element) => {
                element.classList.toggle('hidden', element.dataset.miniIcon !== icon);
            });
        }

        if (miniState) {
            miniState.textContent = message;
        }

        if (miniToggle) {
            miniToggle.setAttribute('aria-label', text);
            miniToggle.setAttribute('aria-pressed', playing ? 'true' : 'false');
        }

        stateTexts.forEach((element) => {
            element.textContent = message;
        });
    }

    function isMuted() {
        return storedMuted || storedVolume === 0;
    }

    function renderVolume() {
        const percent = Math.round(storedVolume * 100);
        const muted = isMuted();

        try {
            audio.volume = storedVolume;
        } catch {
            // Some mobile browsers ignore programmatic volume changes.
        }

        audio.muted = muted;
        player.dataset.volumeMuted = muted ? 'true' : 'false';

        if (volume) {
            volume.value = String(percent);
            volume.style.setProperty('--volume', `${percent}%`);
            volume.setAttribute('aria-valuetext', `${percent} %${muted ? ' (silenciado)' : ''}`);
        }

        if (volumeValue) {
            volumeValue.textContent = muted ? 'Silenciado' : `${percent} %`;
        }

        [mute, miniMute].forEach((button) => {
            if (!button) {
                return;
            }

            button.setAttribute('aria-pressed', muted ? 'true' : 'false');
            button.setAttribute('aria-label', muted ? 'Activar sonido' : 'Silenciar');
            button.setAttribute('title', muted ? 'Activar sonido' : 'Silenciar');
            button.querySelectorAll('[data-volume-icon], [data-mini-volume-icon]').forEach((element) => {
                const key = element.dataset.volumeIcon ?? element.dataset.miniVolumeIcon;

                element.classList.toggle('hidden', key !== (muted ? 'off' : 'on'));
            });
        });
    }

    function toggleMute() {
        if (isMuted()) {
            storedMuted = false;

            if (storedVolume === 0) {
                storedVolume = lastNonZero > 0 ? lastNonZero : DEFAULT_VOLUME;
            }
        } else {
            storedMuted = true;
        }

        renderVolume();
        persist(VOLUME_KEY, storedVolume);
        persist(MUTED_KEY, storedMuted);
    }

    function showMini() {
        if (!mini || !mini.hidden) {
            return;
        }

        mini.hidden = false;
        document.body.dataset.miniPlayerActive = 'true';
    }

    async function start() {
        if (audio.error) {
            audio.removeAttribute('src');
            audio.load();
            loading.loaded = false;
        }

        if (!loading.loaded) {
            audio.src = url;
            loading.loaded = true;
            renderVolume();
        }

        try {
            setState('loading', MESSAGES.loading);
            await audio.play();
        } catch {
            setState('error', MESSAGES.error);
        }
    }

    function togglePlayback() {
        if (!audio.paused) {
            audio.pause();

            return;
        }

        void start();
    }

    function announce(message) {
        if (shareStatus) {
            shareStatus.textContent = message;
        }
    }

    async function handleShare() {
        const shareUrl = `${window.location.origin}${window.location.pathname}`;

        if (typeof navigator.share === 'function') {
            try {
                await navigator.share({
                    title: document.title,
                    text: 'Escuchá la radio desde el navegador.',
                    url: shareUrl,
                });

                return;
            } catch (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }
            }
        }

        try {
            if (!navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') {
                throw new Error('clipboard-unavailable');
            }

            await navigator.clipboard.writeText(shareUrl);
            announce(SHARE.copied);

            return;
        } catch {
            // Fall through to the selectable link.
        }

        if (shareFallback && shareInput) {
            shareFallback.hidden = false;
            shareInput.value = shareUrl;
            shareInput.focus();
            shareInput.select();
            announce(SHARE.fallback);
        }
    }

    if (url === '') {
        setState('error', MESSAGES.error);
        toggle.disabled = true;

        return;
    }

    if (format === 'hls' && audio.canPlayType(HLS_MIME) === '') {
        setState('error', MESSAGES.unsupportedHls);
        toggle.disabled = true;

        return;
    }

    volume?.addEventListener('input', () => {
        const percent = clamp(Number(volume.value), 0, 100);
        storedVolume = percent / 100;

        if (storedVolume > 0) {
            lastNonZero = storedVolume;
            storedMuted = false;
        } else {
            storedMuted = true;
        }

        renderVolume();
    });

    volume?.addEventListener('change', () => {
        persist(VOLUME_KEY, storedVolume);
        persist(MUTED_KEY, storedMuted);
    });

    mute?.addEventListener('click', toggleMute);
    miniMute?.addEventListener('click', toggleMute);

    toggle.addEventListener('click', togglePlayback);
    miniToggle?.addEventListener('click', togglePlayback);
    share?.addEventListener('click', () => void handleShare());

    starters.forEach((starter) => {
        starter.addEventListener('click', () => {
            void start();
        });
    });

    audio.addEventListener('loadstart', () => setState('loading', MESSAGES.loading));
    audio.addEventListener('waiting', () => {
        if (player.dataset.playerState !== 'reconnecting' && player.dataset.playerState !== 'error') {
            setState('loading', MESSAGES.loading);
        }
    });
    audio.addEventListener('playing', () => {
        loading.hasPlayed = true;
        setState('playing', MESSAGES.playing);
        showMini();
    });
    audio.addEventListener('pause', () => {
        if (!audio.ended && !audio.error && player.dataset.playerState !== 'error') {
            setState('paused', MESSAGES.paused);
        }
    });
    audio.addEventListener('stalled', () => {
        if (player.dataset.playerState !== 'error') {
            setState('reconnecting', loading.hasPlayed ? MESSAGES.reconnecting : MESSAGES.loading);
        }
    });
    audio.addEventListener('error', () => setState('error', MESSAGES.error));
    audio.addEventListener('ended', () => setState('paused', MESSAGES.paused));

    renderVolume();
    setState('idle', MESSAGES.idle);
}

document.querySelectorAll('[data-player]').forEach(initPlayer);
