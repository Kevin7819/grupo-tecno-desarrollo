<?php

namespace App\Application\Cms;

final readonly class HostInput
{
    public function __construct(
        public string $name,
        public ?string $bio,
        public ?string $photoPath,
        public bool $isActive,
    ) {}
}
