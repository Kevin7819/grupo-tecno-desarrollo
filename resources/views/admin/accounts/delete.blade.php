@extends('layouts.admin')

@section('title', 'Eliminar cuenta administrativa')

@section('content')
    <h1 class="text-3xl font-semibold">Eliminar cuenta administrativa</h1>

    <div class="surface-card mt-6 max-w-2xl">
        <p class="text-ink">
            Vas a eliminar la cuenta de <span class="font-semibold">«{{ $account->name }}»</span>
            ({{ $account->email }}) de forma permanente.
        </p>

        @if ($isCurrent)
            <p class="mt-4 text-sm text-danger">
                Esta es la cuenta con la que tenés la sesión iniciada: no se puede eliminar.
            </p>
        @elseif ($accountCount <= 1)
            <p class="mt-4 text-sm text-danger">
                Es la última cuenta administrativa: eliminarla dejaría el panel sin acceso.
            </p>
        @else
            <p class="mt-2 text-sm text-ink-muted">
                La persona pierde el acceso al panel inmediatamente. Esta acción no se puede deshacer.
            </p>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            @if (! $isCurrent && $accountCount > 1)
                <form method="POST" action="{{ route('admin.accounts.destroy', $account->id) }}">
                    @csrf
                    @method('DELETE')

                    <x-ui.button class="bg-danger text-surface-base hover:bg-danger/80">Eliminar definitivamente</x-ui.button>
                </form>
            @endif

            <a href="{{ route('admin.accounts.index') }}" class="action-secondary">Cancelar</a>
        </div>
    </div>
@endsection
