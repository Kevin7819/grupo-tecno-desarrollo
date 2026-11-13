@extends('layouts.admin')

@section('title', $program === null ? 'Nuevo programa — Panel administrativo' : 'Editar programa — Panel administrativo')

@section('content')
    @php
        $control = 'mt-1 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink placeholder:text-ink-muted/60';
        $error = 'mt-1 text-xs text-danger';

        $existingRows = $program?->schedule ?? [];
        $rows = old('schedule');

        if (! is_array($rows) || $rows === []) {
            $rows = $existingRows === []
                ? [['weekday' => '', 'starts_at' => '', 'ends_at' => '']]
                : array_map(static fn ($slot): array => [
                    'weekday' => $slot->weekday->value,
                    'starts_at' => $slot->startsAt,
                    'ends_at' => $slot->endsAt,
                ], $existingRows);
        }

        $rows = array_values($rows);
        $selectedHosts = array_map('intval', (array) old('hosts', $program?->hostIds() ?? []));
        $selectedStatus = old('status', $program?->status->value ?? \App\Domain\Cms\ProgramStatus::Draft->value);
    @endphp

    <h1 class="text-3xl font-semibold">{{ $program === null ? 'Nuevo programa' : 'Editar programa' }}</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        Definí el nombre, las franjas horarias y los locutores. El programa solo se muestra en la página pública cuando su estado es «Publicado».
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST"
          action="{{ $program === null ? route('admin.programs.store') : route('admin.programs.update', $program->id) }}"
          enctype="multipart/form-data"
          class="mt-8 grid gap-4 lg:grid-cols-2">
        @csrf
        @if ($program !== null)
            @method('PUT')
        @endif

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Datos del programa</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-ink">Nombre</label>
                    <x-ui.input name="name" :value="old('name', $program?->name)" maxlength="120" required />
                    @error('name')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-ink">Descripción</label>
                    <textarea name="description" id="description" rows="4" maxlength="2000" class="{{ $control }}">{{ old('description', $program?->description) }}</textarea>
                    @error('description')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="status" class="block text-sm font-medium text-ink">Estado</label>
                        <select name="status" id="status" class="{{ $control }}">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="{{ $error }}">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-ink">Orden de visualización</label>
                        <x-ui.input name="sort_order" type="number" min="0" max="9999" :value="old('sort_order', $program?->sortOrder ?? 0)" />
                        @error('sort_order')<p class="{{ $error }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <span class="block text-sm font-medium text-ink">Portada</span>

                    @if ($program?->coverPath !== null)
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ Storage::disk('public')->url($program->coverPath) }}"
                                 alt="Portada actual"
                                 class="h-16 w-auto rounded-lg border border-surface-border object-cover">
                            <label class="flex items-center gap-2 text-sm text-ink-muted">
                                <input type="checkbox" name="remove_cover" value="1" class="size-4 rounded border-surface-border bg-surface-base">
                                Quitar portada
                            </label>
                        </div>
                    @endif

                    <input type="file" name="cover" id="cover" accept="image/jpeg,image/png,image/webp"
                           class="mt-2 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink file:mr-3 file:rounded-md file:border-0 file:bg-surface-border file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink">
                    <p class="mt-1 text-xs text-ink-muted">JPG, PNG o WebP. Máximo 2 MB.</p>
                    @error('cover')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Franjas horarias</h2>
            <p class="mt-2 text-sm text-ink-muted">
                Una franja por cada día en que se emite. Las franjas completamente vacías se ignoran.
            </p>

            @error('schedule')<p class="mt-2 text-xs text-danger">{{ $message }}</p>@enderror

            <div data-schedule data-next-index="{{ count($rows) }}" class="mt-4 space-y-3">
                <div data-schedule-rows class="space-y-3">
                    @foreach ($rows as $index => $row)
                        <div data-schedule-row class="grid gap-2 sm:grid-cols-[1fr_auto_auto_auto]">
                            <div>
                                <label class="sr-only" for="schedule-{{ $index }}-weekday">Día</label>
                                <select name="schedule[{{ $index }}][weekday]" id="schedule-{{ $index }}-weekday" class="{{ $control }} mt-0">
                                    <option value="">Elegí un día</option>
                                    @foreach ($weekdays as $weekday)
                                        <option value="{{ $weekday->value }}" @selected((string) ($row['weekday'] ?? '') === (string) $weekday->value)>
                                            {{ $weekday->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="sr-only" for="schedule-{{ $index }}-starts">Hora de inicio</label>
                                <input type="time" name="schedule[{{ $index }}][starts_at]" id="schedule-{{ $index }}-starts"
                                       value="{{ $row['starts_at'] ?? '' }}" class="{{ $control }} mt-0">
                            </div>

                            <div>
                                <label class="sr-only" for="schedule-{{ $index }}-ends">Hora de fin</label>
                                <input type="time" name="schedule[{{ $index }}][ends_at]" id="schedule-{{ $index }}-ends"
                                       value="{{ $row['ends_at'] ?? '' }}" class="{{ $control }} mt-0">
                            </div>

                            <button type="button" data-schedule-remove class="action-ghost min-h-11">Quitar</button>
                        </div>

                        @error("schedule.{$index}.ends_at")<p class="{{ $error }}">{{ $message }}</p>@enderror
                        @error("schedule.{$index}.starts_at")<p class="{{ $error }}">{{ $message }}</p>@enderror
                        @error("schedule.{$index}.weekday")<p class="{{ $error }}">{{ $message }}</p>@enderror
                    @endforeach
                </div>

                <button type="button" data-schedule-add class="action-secondary">Añadir otra franja</button>

                <template data-schedule-template>
                    <div data-schedule-row class="grid gap-2 sm:grid-cols-[1fr_auto_auto_auto]">
                        <div>
                            <label class="sr-only" for="schedule-__index__-weekday">Día</label>
                            <select name="schedule[__index__][weekday]" id="schedule-__index__-weekday" class="{{ $control }} mt-0">
                                <option value="">Elegí un día</option>
                                @foreach ($weekdays as $weekday)
                                    <option value="{{ $weekday->value }}">{{ $weekday->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="sr-only" for="schedule-__index__-starts">Hora de inicio</label>
                            <input type="time" name="schedule[__index__][starts_at]" id="schedule-__index__-starts" class="{{ $control }} mt-0">
                        </div>

                        <div>
                            <label class="sr-only" for="schedule-__index__-ends">Hora de fin</label>
                            <input type="time" name="schedule[__index__][ends_at]" id="schedule-__index__-ends" class="{{ $control }} mt-0">
                        </div>

                        <button type="button" data-schedule-remove class="action-ghost min-h-11">Quitar</button>
                    </div>
                </template>
            </div>
        </section>

        <section class="surface-card lg:col-span-2">
            <h2 class="text-lg font-semibold">Locutores</h2>

            @if ($hosts === [])
                <p class="mt-2 text-sm text-ink-muted">
                    Todavía no hay locutores registrados.
                    <a href="{{ route('admin.hosts.create') }}" class="font-medium text-brand underline decoration-brand/60 underline-offset-4">Crear un locutor</a>
                    para poder asociarlo a este programa.
                </p>
            @else
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($hosts as $host)
                        <label class="flex items-start gap-3 rounded-lg border border-surface-border bg-surface-base p-3 text-sm">
                            <input type="checkbox" name="hosts[]" value="{{ $host->id }}"
                                   @checked(in_array($host->id, $selectedHosts, true))
                                   class="mt-0.5 size-4 rounded border-surface-border bg-surface-base">
                            <span>
                                <span class="block font-medium text-ink">{{ $host->name }}</span>
                                @if (! $host->isActive)
                                    <span class="block text-xs text-ink-muted">Inactivo: no se publica su nombre.</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="flex flex-wrap gap-3 lg:col-span-2">
            <x-ui.button type="submit">{{ $program === null ? 'Crear programa' : 'Guardar cambios' }}</x-ui.button>
            <a href="{{ route('admin.programs.index') }}" class="action-secondary">Cancelar</a>
        </div>
    </form>
@endsection
