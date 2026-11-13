@extends('layouts.admin')

@section('title', 'Eliminar enlace social')

@section('content')
    <h1 class="text-3xl font-semibold">Eliminar enlace social</h1>

    <div class="surface-card mt-6 max-w-2xl">
        <p class="text-ink">
            Vas a eliminar <span class="font-semibold">«{{ $link->label }}»</span> de forma permanente.
        </p>
        <p class="mt-2 break-all text-sm text-ink-muted">{{ $link->url }}</p>
        <p class="mt-2 text-sm text-ink-muted">
            Si no queda ningún enlace guardado, la página pública vuelve a usar la configuración del servidor.
        </p>

        <form method="POST" action="{{ route('admin.contact.links.destroy', $link->id) }}" class="mt-6 flex flex-wrap gap-3">
            @csrf
            @method('DELETE')

            <x-ui.button class="bg-danger text-surface-base hover:bg-danger/80">Eliminar definitivamente</x-ui.button>
            <a href="{{ route('admin.contact.edit') }}" class="action-secondary">Cancelar</a>
        </form>
    </div>
@endsection
