@extends('layouts.admin')

@section('title', 'Eliminar programa — Panel administrativo')

@section('content')
    <h1 class="text-3xl font-semibold">Eliminar programa</h1>

    <div class="surface-card mt-6 max-w-2xl">
        <p class="text-ink">
            Vas a eliminar <span class="font-semibold">«{{ $program->name }}»</span> de forma permanente.
        </p>
        <p class="mt-2 text-sm text-ink-muted">
            También se eliminan sus franjas horarias y sus asociaciones con locutores. Los locutores no se borran.
            Esta acción no se puede deshacer.
        </p>

        @if ($program->isPublished())
            <p class="mt-4 text-sm text-warning">
                El programa está publicado: al eliminarlo desaparece de la página pública.
            </p>
        @endif

        <form method="POST" action="{{ route('admin.programs.destroy', $program->id) }}" class="mt-6 flex flex-wrap gap-3">
            @csrf
            @method('DELETE')

            <x-ui.button class="bg-danger text-surface-base hover:bg-danger/80">Eliminar definitivamente</x-ui.button>
            <a href="{{ route('admin.programs.index') }}" class="action-secondary">Cancelar</a>
        </form>
    </div>
@endsection
