<?php

namespace App\Domain\Cms;

/**
 * A public gallery image managed from the admin panel.
 */
final readonly class GalleryImage
{
    private function __construct(
        public ?int $id,
        public string $title,
        public string $imagePath,
        public string $altText,
        public ?string $caption,
        public int $sortOrder,
        public bool $isPublished,
    ) {}

    public static function fromValues(
        ?int $id,
        ?string $title,
        ?string $imagePath,
        ?string $altText,
        ?string $caption,
        int $sortOrder = 0,
        bool $isPublished = true,
    ): self {
        return new self(
            id: $id,
            title: trim((string) $title),
            imagePath: trim((string) $imagePath),
            altText: trim((string) $altText),
            caption: self::nullable($caption),
            sortOrder: max(0, $sortOrder),
            isPublished: $isPublished,
        );
    }

    private static function nullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
