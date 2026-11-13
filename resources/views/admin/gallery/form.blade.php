@extends('layouts.admin')

@section('title', $image === null ? 'Nueva imagen — Galería' : 'Editar imagen — Galería')

@section('content')
    @php
        $error = 'mt-1 text-xs text-danger';
        $control = 'mt-1 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink placeholder:text-ink-muted/60';
    @endphp

    <h1 class="text-3xl font-semibold">{{ $image === null ? 'Agregar imagen' : 'Editar imagen' }}</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        La imagen se publica en la galería de la portada cuando marcás «Publicada».
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST"
          action="{{ $image === null ? route('admin.gallery.store') : route('admin.gallery.update', $image->id) }}"
          enctype="multipart/form-data"
          class="surface-card mt-8 grid gap-4 lg:grid-cols-2">
        @csrf
        @if ($image !== null)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="block text-sm font-medium text-ink">Título</label>
            <x-ui.input name="title" :value="old('title', $image?->title)" maxlength="120" />
            @error('title')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-ink">Orden de visualización</label>
            <x-ui.input name="sort_order" type="number" :value="old('sort_order', $image?->sortOrder ?? 0)" min="0" max="9999" />
            @error('sort_order')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div class="lg:col-span-2">
            <label for="image" class="block text-sm font-medium text-ink">Archivo de imagen</label>

            @if ($image !== null)
                <img src="{{ Storage::disk('public')->url($image->imagePath) }}"
                     alt="{{ $image->altText }}"
                     class="mt-2 h-28 w-auto rounded-lg border border-surface-border object-cover">
            @endif

            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
                   class="mt-2 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink file:mr-3 file:rounded-md file:border-0 file:bg-surface-border file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink">
            <p class="mt-1 text-xs text-ink-muted">
                JPG, PNG o WebP. Máximo 2 MB. {{ $image !== null ? 'Dejá el campo vacío para conservar la imagen actual.' : '' }}
            </p>
            @error('image')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="alt_text" class="block text-sm font-medium text-ink">Texto alternativo</label>
            <x-ui.input name="alt_text" :value="old('alt_text', $image?->altText)" maxlength="200" />
            <p class="mt-1 text-xs text-ink-muted">Describe la imagen para quienes usan lectores de pantalla.</p>
            @error('alt_text')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="caption" class="block text-sm font-medium text-ink">Leyenda (opcional)</label>
            <x-ui.input name="caption" :value="old('caption', $image?->caption)" maxlength="200" />
            @error('caption')<p class="{{ $error }}">{{ $message }}</p>@enderror
        </div>

        <div class="lg:col-span-2">
            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-surface-border bg-surface-base"
                       @checked(old('is_published', $image?->isPublished ?? true))>
                Publicada en la galería pública
            </label>
        </div>

        <div class="flex flex-wrap gap-3 lg:col-span-2">
            <x-ui.button type="submit">{{ $image === null ? 'Agregar imagen' : 'Guardar cambios' }}</x-ui.button>
            <a href="{{ route('admin.gallery.index') }}" class="action-secondary">Volver a la galería</a>
        </div>
    </form>
@endsection
