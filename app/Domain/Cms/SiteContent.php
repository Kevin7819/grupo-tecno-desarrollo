<?php

namespace App\Domain\Cms;

/**
 * Editable copy of the public homepage.
 *
 * Every field is optional. When nothing is stored, or a field is empty, the
 * public page renders the original neutral text of the project: the defaults
 * live here so the admin form and the view never disagree.
 */
final readonly class SiteContent
{
    public const DEFAULT_HERO_TITLE = 'Tu música, tu momento';

    public const DEFAULT_HERO_INTRO = 'Grupo Tecno Desarrollo es un proyecto de radio por Internet. Abrí el reproductor y escuchá desde el navegador, en la computadora o en el teléfono. La programación definitiva se irá publicando a medida que esté aprobada.';

    public const DEFAULT_ABOUT_TITLE = 'Sobre la emisora';

    public const DEFAULT_ABOUT_TEXT = 'Grupo Tecno Desarrollo es un proyecto de radio por Internet en construcción. La idea es simple: una señal de audio que se escucha desde el navegador, sin instalar aplicaciones. La identidad, la programación y los servicios definitivos se publicarán cuando estén aprobados.';

    public const DEFAULT_GALLERY_TITLE = 'Identidad y entorno sonoro';

    public const DEFAULT_GALLERY_INTRO = 'Composiciones gráficas construidas con los recursos de marca del proyecto. Todavía no hay fotografías de cabina ni de producción.';

    public const DEFAULT_CONTACT_TITLE = 'Comunidad y contacto';

    public const DEFAULT_CONTACT_INTRO = 'Los canales oficiales se publicarán en esta sección cuando estén confirmados. Mientras tanto, no hay cuentas ni direcciones de contacto públicas que mostrar.';

    private function __construct(
        public ?string $heroKicker,
        public ?string $heroTitle,
        public ?string $heroIntro,
        public ?string $aboutTitle,
        public ?string $aboutText,
        public ?string $galleryTitle,
        public ?string $galleryIntro,
        public ?string $contactTitle,
        public ?string $contactIntro,
    ) {}

    public static function fromValues(
        ?string $heroKicker = null,
        ?string $heroTitle = null,
        ?string $heroIntro = null,
        ?string $aboutTitle = null,
        ?string $aboutText = null,
        ?string $galleryTitle = null,
        ?string $galleryIntro = null,
        ?string $contactTitle = null,
        ?string $contactIntro = null,
    ): self {
        return new self(
            heroKicker: self::text($heroKicker),
            heroTitle: self::text($heroTitle),
            heroIntro: self::text($heroIntro),
            aboutTitle: self::text($aboutTitle),
            aboutText: self::text($aboutText),
            galleryTitle: self::text($galleryTitle),
            galleryIntro: self::text($galleryIntro),
            contactTitle: self::text($contactTitle),
            contactIntro: self::text($contactIntro),
        );
    }

    public static function defaults(): self
    {
        return new self(null, null, null, null, null, null, null, null, null);
    }

    public function heroKickerText(?string $fallback = null): string
    {
        return $this->heroKicker ?? self::text($fallback) ?? 'Radio por Internet';
    }

    public function heroTitleText(): string
    {
        return $this->heroTitle ?? self::DEFAULT_HERO_TITLE;
    }

    public function heroIntroText(): string
    {
        return $this->heroIntro ?? self::DEFAULT_HERO_INTRO;
    }

    public function aboutTitleText(): string
    {
        return $this->aboutTitle ?? self::DEFAULT_ABOUT_TITLE;
    }

    public function aboutTextValue(?string $stationDescription): string
    {
        return $this->aboutText ?? self::text($stationDescription) ?? self::DEFAULT_ABOUT_TEXT;
    }

    public function galleryTitleText(): string
    {
        return $this->galleryTitle ?? self::DEFAULT_GALLERY_TITLE;
    }

    public function galleryIntroText(): string
    {
        return $this->galleryIntro ?? self::DEFAULT_GALLERY_INTRO;
    }

    public function contactTitleText(): string
    {
        return $this->contactTitle ?? self::DEFAULT_CONTACT_TITLE;
    }

    public function contactIntroText(): string
    {
        return $this->contactIntro ?? self::DEFAULT_CONTACT_INTRO;
    }

    private static function text(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
