@extends('layouts.admin')

@section('title', 'Eliminar locutor — Panel administrativo')

@section('content')
    <h1 class="text-3xl font-semibold">Eliminar locutor</h1>

    <div class="surface-card mt-6 max-w-2xl">
        <p class="text-ink">
            Vas a eliminar a <span class="font-semibold">«{{ $host->name }}»</span> de forma permanente.
        </p>
        <p class="mt-2 text-sm text-ink-muted">
            Los programas que lo tienen asociado se conservan; solo se quita la asociación. Esta acción no se puede deshacer.
        </p>

        <form method="POST" action="{{ route('admin.hosts.destroy', $host->id) }}" class="mt-6 flex flex-wrap gap-3">
            @csrf
            @method('DELETE')

            <x-ui.button class="bg-danger text-surface-base hover:bg-danger/80">Eliminar definitivamente</x-ui.button>
            <a href="{{ route('admin.hosts.index') }}" class="action-secondary">Cancelar</a>
        </form>
    </div>
@endsection
