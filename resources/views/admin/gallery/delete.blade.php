@extends('layouts.admin')

@section('title', 'Eliminar imagen — Galería')

@section('content')
    <h1 class="text-3xl font-semibold">Eliminar imagen</h1>

    <div class="surface-card mt-6 max-w-2xl">
        <p class="text-ink">
            Vas a eliminar <span class="font-semibold">«{{ $image->title }}»</span> de forma permanente.
        </p>
        <p class="mt-2 text-sm text-ink-muted">
            La imagen se borra del almacenamiento y deja de aparecer en la galería pública. Esta acción no se puede
            deshacer.
        </p>

        <img src="{{ Storage::disk('public')->url($image->imagePath) }}"
             alt="{{ $image->altText }}"
             class="mt-4 h-24 w-auto rounded-lg border border-surface-border object-cover">

        <form method="POST" action="{{ route('admin.gallery.destroy', $image->id) }}" class="mt-6 flex flex-wrap gap-3">
            @csrf
            @method('DELETE')

            <x-ui.button class="bg-danger text-surface-base hover:bg-danger/80">Eliminar definitivamente</x-ui.button>
            <a href="{{ route('admin.gallery.index') }}" class="action-secondary">Cancelar</a>
        </form>
    </div>
@endsection
