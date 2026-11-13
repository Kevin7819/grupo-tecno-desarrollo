@extends('layouts.admin')

@section('title', 'Configuración de la emisora — Panel administrativo')

@section('content')
    @php
        $control = 'mt-1 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink placeholder:text-ink-muted/60';
        $error = 'mt-1 text-xs text-danger';
    @endphp

    <h1 class="text-3xl font-semibold">Configuración de la emisora</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        Estos valores alimentan la página pública y el reproductor. Lo guardado acá tiene prioridad sobre las
        variables <code class="font-mono text-ink">RADIO_*</code> del servidor; si la URL de transmisión queda
        vacía o no es reproducible, el reproductor usa la configuración del entorno.
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST"
          action="{{ route('admin.settings.update') }}"
          enctype="multipart/form-data"
          class="mt-8 grid gap-4 lg:grid-cols-2">
        @csrf
        @method('PUT')

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Identidad</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="station_name" class="block text-sm font-medium text-ink">Nombre de la emisora</label>
                    <x-ui.input name="station_name" :value="old('station_name', $settings->stationName)" maxlength="120" />
                    @error('station_name')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slogan" class="block text-sm font-medium text-ink">Eslogan</label>
                    <x-ui.input name="slogan" :value="old('slogan', $settings->slogan)" maxlength="160" />
                    @error('slogan')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-ink">Descripción</label>
                    <textarea name="description" id="description" rows="5" maxlength="2000" class="{{ $control }}">{{ old('description', $settings->description) }}</textarea>
                    @error('description')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <span class="block text-sm font-medium text-ink">Logotipo</span>

                    @if ($settings->logoPath !== null)
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ Storage::disk('public')->url($settings->logoPath) }}"
                                 alt="Logotipo actual"
                                 class="h-16 w-auto rounded-lg border border-surface-border bg-surface-base object-contain p-1">
                            <label class="flex items-center gap-2 text-sm text-ink-muted">
                                <input type="checkbox" name="remove_logo" value="1" class="size-4 rounded border-surface-border bg-surface-base">
                                Quitar logotipo
                            </label>
                        </div>
                    @endif

                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/webp"
                           class="mt-2 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink file:mr-3 file:rounded-md file:border-0 file:bg-surface-border file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink">
                    <p class="mt-1 text-xs text-ink-muted">JPG, PNG o WebP. Máximo 2 MB.</p>
                    @error('logo')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Transmisión</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="stream_url" class="block text-sm font-medium text-ink">URL del streaming</label>
                    <x-ui.input name="stream_url" type="url" :value="old('stream_url', $settings->stream->url)" placeholder="https://" maxlength="2048" />
                    <p class="mt-1 text-xs text-ink-muted">
                        Debe empezar con <code class="font-mono">https://</code>. Si la dejás vacía, se usa la URL del entorno.
                    </p>
                    @error('stream_url')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="stream_format" class="block text-sm font-medium text-ink">Formato de la transmisión</label>
                    <select name="stream_format" id="stream_format" class="{{ $control }}">
                        @foreach (\App\Domain\Radio\StreamFormat::cases() as $format)
                            <option value="{{ $format->value }}" @selected(old('stream_format', $settings->stream->format->value) === $format->value)>
                                {{ mb_strtoupper($format->value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('stream_format')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="stream_name" class="block text-sm font-medium text-ink">Nombre de la transmisión</label>
                    <x-ui.input name="stream_name" :value="old('stream_name', $settings->stream->name)" maxlength="120" />
                    <p class="mt-1 text-xs text-ink-muted">
                        Es el nombre que ve la persona que escucha. Identificá las transmisiones externas de prueba como tales.
                    </p>
                    @error('stream_name')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-ink">Estado de la emisora</label>
                    <select name="status" id="status" class="{{ $control }}">
                        @foreach (\App\Domain\Cms\StationStatus::cases() as $stationStatus)
                            <option value="{{ $stationStatus->value }}" @selected(old('status', $settings->status->value) === $stationStatus->value)>
                                {{ $stationStatus->label() }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-ink-muted">
                        Con la emisora en mantenimiento o inactiva, la página pública muestra un aviso y no ofrece el reproductor.
                    </p>
                    @error('status')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 lg:col-span-2">
            <x-ui.button type="submit">Guardar cambios</x-ui.button>
            <a href="{{ route('admin.dashboard') }}" class="action-secondary">Volver al panel</a>
        </div>
    </form>
@endsection
