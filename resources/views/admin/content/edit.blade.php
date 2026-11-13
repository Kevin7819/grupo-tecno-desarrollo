@extends('layouts.admin')

@section('title', 'Contenido público — Panel administrativo')

@section('content')
    @php
        $control = 'mt-1 block w-full rounded-lg border border-surface-border bg-surface-base px-3 py-2 text-sm text-ink placeholder:text-ink-muted/60';
        $error = 'mt-1 text-xs text-danger';
    @endphp

    <h1 class="text-3xl font-semibold">Contenido de la página pública</h1>
    <p class="mt-2 max-w-2xl text-ink-muted">
        Estos textos alimentan la portada del sitio. Si un campo queda vacío, la página conserva el texto
        neutral actual del proyecto.
    </p>

    @if ($errors->any())
        <x-ui.alert variant="error" class="mt-6">Revisá los campos marcados. No se guardó ningún cambio.</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('admin.content.update') }}" class="mt-8 grid gap-4 lg:grid-cols-2">
        @csrf
        @method('PUT')

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Portada</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="hero_kicker" class="block text-sm font-medium text-ink">Frase superior</label>
                    <x-ui.input name="hero_kicker" :value="old('hero_kicker', $content->heroKicker)" maxlength="160" :placeholder="$content->heroKickerText()" />
                    <p class="mt-1 text-xs text-ink-muted">Sin valor, se usa el eslogan de la emisora.</p>
                    @error('hero_kicker')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="hero_title" class="block text-sm font-medium text-ink">Título principal</label>
                    <x-ui.input name="hero_title" :value="old('hero_title', $content->heroTitle)" maxlength="160" :placeholder="$content->heroTitleText()" />
                    @error('hero_title')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="hero_intro" class="block text-sm font-medium text-ink">Introducción</label>
                    <textarea name="hero_intro" id="hero_intro" rows="5" maxlength="1000" class="{{ $control }}" placeholder="{{ $content->heroIntroText() }}">{{ old('hero_intro', $content->heroIntro) }}</textarea>
                    @error('hero_intro')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Sobre la emisora</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="about_title" class="block text-sm font-medium text-ink">Título de la sección</label>
                    <x-ui.input name="about_title" :value="old('about_title', $content->aboutTitle)" maxlength="160" :placeholder="$content->aboutTitleText()" />
                    @error('about_title')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="about_text" class="block text-sm font-medium text-ink">Texto</label>
                    <textarea name="about_text" id="about_text" rows="6" maxlength="2000" class="{{ $control }}" placeholder="{{ $content->aboutTextValue(null) }}">{{ old('about_text', $content->aboutText) }}</textarea>
                    <p class="mt-1 text-xs text-ink-muted">Sin valor, se usa la descripción configurada en Emisora.</p>
                    @error('about_text')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Galería</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="gallery_title" class="block text-sm font-medium text-ink">Título de la sección</label>
                    <x-ui.input name="gallery_title" :value="old('gallery_title', $content->galleryTitle)" maxlength="160" :placeholder="$content->galleryTitleText()" />
                    @error('gallery_title')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="gallery_intro" class="block text-sm font-medium text-ink">Introducción</label>
                    <textarea name="gallery_intro" id="gallery_intro" rows="4" maxlength="1000" class="{{ $control }}" placeholder="{{ $content->galleryIntroText() }}">{{ old('gallery_intro', $content->galleryIntro) }}</textarea>
                    @error('gallery_intro')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="surface-card">
            <h2 class="text-lg font-semibold">Contacto</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="contact_title" class="block text-sm font-medium text-ink">Título de la sección</label>
                    <x-ui.input name="contact_title" :value="old('contact_title', $content->contactTitle)" maxlength="160" :placeholder="$content->contactTitleText()" />
                    @error('contact_title')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_intro" class="block text-sm font-medium text-ink">Introducción</label>
                    <textarea name="contact_intro" id="contact_intro" rows="4" maxlength="1000" class="{{ $control }}" placeholder="{{ $content->contactIntroText() }}">{{ old('contact_intro', $content->contactIntro) }}</textarea>
                    @error('contact_intro')<p class="{{ $error }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 lg:col-span-2">
            <x-ui.button type="submit">Guardar contenido</x-ui.button>
            <a href="{{ route('home') }}" class="action-secondary" target="_blank" rel="noopener noreferrer">Ver página pública</a>
        </div>
    </form>
@endsection
