<?php

namespace App\Application\Cms;

final readonly class GalleryImageInput
{
    public function __construct(
        public string $title,
        public string $imagePath,
        public string $altText,
        public ?string $caption,
        public int $sortOrder,
        public bool $isPublished,
    ) {}
}
