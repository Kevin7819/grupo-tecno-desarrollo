@extends('layouts.admin')

@section('title', 'Galería — Panel administrativo')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="section-eyebrow">Galería</p>
            <h1 class="mt-2 text-3xl font-semibold">Imágenes</h1>
            <p class="mt-2 max-w-2xl text-ink-muted">
                Solo las imágenes publicadas se muestran en la portada. Sin imágenes publicadas, el sitio conserva
                las composiciones gráficas actuales.
            </p>
        </div>

        <a href="{{ route('admin.gallery.create') }}" class="action-primary">Agregar imagen</a>
    </div>

    @if ($images === [])
        <div class="surface-card mt-8">
            <h2 class="text-lg font-semibold">Todavía no hay imágenes</h2>
            <p class="mt-2 text-sm text-ink-muted">
                Subí una fotografía aprobada y publicala cuando esté lista para la portada.
            </p>
            <a href="{{ route('admin.gallery.create') }}" class="action-secondary mt-4">Agregar imagen</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($images as $image)
                <article class="surface-card">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex min-w-0 items-start gap-4">
                            <img src="{{ Storage::disk('public')->url($image->imagePath) }}"
                                 alt="{{ $image->altText }}"
                                 class="h-20 w-28 shrink-0 rounded-lg border border-surface-border object-cover">

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-semibold">{{ $image->title }}</h2>
                                    @if ($image->isPublished)
                                        <span class="inline-flex items-center rounded-full border border-success/40 bg-success/10 px-2.5 py-0.5 text-xs font-semibold text-success">Publicada</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-surface-border bg-surface-border/40 px-2.5 py-0.5 text-xs font-semibold text-ink-muted">Oculta</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-ink-muted">Orden de visualización: {{ $image->sortOrder }}</p>
                                @if ($image->caption !== null)
                                    <p class="mt-2 text-sm text-ink-muted">{{ $image->caption }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.gallery.edit', $image->id) }}" class="action-secondary min-h-11 px-3 py-2">Editar</a>
                            <a href="{{ route('admin.gallery.delete', $image->id) }}" class="action-ghost">Eliminar</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
