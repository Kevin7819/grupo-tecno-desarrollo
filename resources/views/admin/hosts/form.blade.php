@extends('layouts.admin')

@section('title', $host === null ? 'Nuevo locutor — Panel administrativo' : 'Editar locutor — Panel administrativo')

@section('content')
    @php
        $control = 'mt-1 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink placeholder:text-ink-muted/60';
        $error = 'mt-1 text-xs text-danger';
    @endphp

    <h1 class="text-3xl font-semibold">{{ $host === null ? 'Nuevo locutor' : 'Editar locutor' }}</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        El nombre y la biografía se publican junto al programa cuando el locutor está activo.
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST"
          action="{{ $host === null ? route('admin.hosts.store') : route('admin.hosts.update', $host->id) }}"
          enctype="multipart/form-data"
          class="surface-card mt-8 max-w-2xl">
        @csrf
        @if ($host !== null)
            @method('PUT')
        @endif

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-ink">Nombre</label>
                <x-ui.input name="name" :value="old('name', $host?->name)" maxlength="120" required />
                @error('name')<p class="{{ $error }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-ink">Biografía</label>
                <textarea name="bio" id="bio" rows="4" maxlength="2000" class="{{ $control }}">{{ old('bio', $host?->bio) }}</textarea>
                @error('bio')<p class="{{ $error }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <span class="block text-sm font-medium text-ink">Foto</span>

                @if ($host?->photoPath !== null)
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ Storage::disk('public')->url($host->photoPath) }}"
                             alt="Foto actual"
                             class="size-16 rounded-xl border border-surface-border object-cover">
                        <label class="flex items-center gap-2 text-sm text-ink-muted">
                            <input type="checkbox" name="remove_photo" value="1" class="size-4 rounded border-surface-border bg-surface-base">
                            Quitar foto
                        </label>
                    </div>
                @endif

                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
                       class="mt-2 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink file:mr-3 file:rounded-md file:border-0 file:bg-surface-border file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink">
                <p class="mt-1 text-xs text-ink-muted">JPG, PNG o WebP. Máximo 2 MB.</p>
                @error('photo')<p class="{{ $error }}">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $host?->isActive ?? true))
                       class="size-4 rounded border-surface-border bg-surface-base">
                Locutor activo (visible en la página pública)
            </label>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <x-ui.button type="submit">{{ $host === null ? 'Crear locutor' : 'Guardar cambios' }}</x-ui.button>
            <a href="{{ route('admin.hosts.index') }}" class="action-secondary">Cancelar</a>
        </div>
    </form>
@endsection
