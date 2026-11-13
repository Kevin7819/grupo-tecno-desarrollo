@extends('layouts.admin')

@section('title', 'Redes sociales y contacto — Panel administrativo')

@section('content')
    @php
        $error = 'mt-1 text-xs text-danger';
    @endphp

    <h1 class="text-3xl font-semibold">Redes sociales y contacto</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        Lo guardado acá tiene prioridad sobre la configuración <code class="font-mono text-ink">RADIO_CONTACT_*</code>
        y <code class="font-mono text-ink">RADIO_SOCIAL_*</code> del servidor. Si no hay ningún valor guardado ni de
        entorno, el canal se oculta en la página pública.
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <div class="mt-8 grid gap-4 lg:grid-cols-2">
        <form method="POST" action="{{ route('admin.contact.update') }}" class="surface-card">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold">Contacto</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-ink">Correo de contacto</label>
                    <x-ui.input name="email" type="email" :value="old('email', $details->email)" maxlength="255" />
                    @error('email')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-ink">Teléfono (opcional)</label>
                    <x-ui.input name="phone" type="tel" :value="old('phone', $details->phone)" maxlength="30" placeholder="+54 11 0000 0000" />
                    <p class="mt-1 text-xs text-ink-muted">Se publica como enlace para llamar, solo si está configurado.</p>
                    @error('phone')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-5">
                <x-ui.button type="submit">Guardar contacto</x-ui.button>
            </div>
        </form>

        <section class="surface-card">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Redes sociales</h2>
                <a href="{{ route('admin.contact.links.create') }}" class="action-secondary px-3 py-2">Nuevo enlace</a>
            </div>

            @if ($links === [])
                <p class="mt-4 text-sm text-ink-muted">
                    Todavía no hay enlaces cargados. Mientras no existan, la página usa la configuración del servidor.
                </p>
            @else
                <ul class="mt-4 divide-y divide-surface-border">
                    @foreach ($links as $link)
                        <li class="flex flex-wrap items-start justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold">{{ $link->label }}</span>
                                    @if ($link->isActive)
                                        <span class="inline-flex items-center rounded-full border border-success/40 bg-success/10 px-2.5 py-0.5 text-xs font-semibold text-success">Visible</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-surface-border bg-surface-border/40 px-2.5 py-0.5 text-xs font-semibold text-ink-muted">Oculto</span>
                                    @endif
                                </div>
                                <p class="mt-1 break-all text-xs text-ink-muted">{{ $link->url }}</p>
                                <p class="mt-1 text-xs text-ink-muted">Orden de visualización: {{ $link->sortOrder }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.contact.links.edit', $link->id) }}" class="action-secondary min-h-11 px-3 py-2">Editar</a>
                                <a href="{{ route('admin.contact.links.delete', $link->id) }}" class="action-ghost">Eliminar</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
