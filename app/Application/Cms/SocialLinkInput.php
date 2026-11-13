<?php

namespace App\Application\Cms;

final readonly class SocialLinkInput
{
    public function __construct(
        public string $label,
        public string $url,
        public int $sortOrder,
        public bool $isActive,
    ) {}
}
