@extends('layouts.admin')

@section('title', $link === null ? 'Nuevo enlace social' : 'Editar enlace social')

@section('content')
    @php
        $error = 'mt-1 text-xs text-danger';
    @endphp

    <h1 class="text-3xl font-semibold">{{ $link === null ? 'Nuevo enlace social' : 'Editar enlace social' }}</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        Solo se publican enlaces <code class="font-mono text-ink">https://</code>. El nombre es el texto que ve la
        persona que visita el sitio.
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST"
          action="{{ $link === null ? route('admin.contact.links.store') : route('admin.contact.links.update', $link->id) }}"
          class="surface-card mt-8 grid max-w-2xl gap-4">
        @csrf
        @if ($link !== null)
            @method('PUT')
        @endif

        <div>
            <label for="label" class="block text-sm font-medium text-ink">Red o canal</label>
            <x-ui.input name="label" :value="old('label', $link?->label)" maxlength="60" list="social-networks" />
            <datalist id="social-networks">
                <option value="Instagram"></option>
                <option value="Facebook"></option>
                <option value="TikTok"></option>
                <option value="YouTube"></option>
                <option value="WhatsApp"></option>
                <option value="X"></option>
            </datalist>
            @error('label')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="url" class="block text-sm font-medium text-ink">Enlace del perfil</label>
            <x-ui.input name="url" type="url" :value="old('url', $link?->url)" maxlength="2048" placeholder="https://" />
            @error('url')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-ink">Orden de visualización</label>
            <x-ui.input name="sort_order" type="number" :value="old('sort_order', $link?->sortOrder ?? 0)" min="0" max="9999" />
            @error('sort_order')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input type="checkbox" name="is_active" value="1" class="size-4 rounded border-surface-border bg-surface-base"
                       @checked(old('is_active', $link?->isActive ?? true))>
                Visible en la página pública
            </label>
        </div>

        <div class="flex flex-wrap gap-3">
            <x-ui.button type="submit">{{ $link === null ? 'Agregar enlace' : 'Guardar cambios' }}</x-ui.button>
            <a href="{{ route('admin.contact.edit') }}" class="action-secondary">Volver a contacto</a>
        </div>
    </form>
@endsection
