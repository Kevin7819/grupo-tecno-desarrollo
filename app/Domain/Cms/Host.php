<?php

namespace App\Domain\Cms;

use InvalidArgumentException;

/**
 * A person who presents programs. The extended hosts module arrives in a
 * later delivery; this value only carries what the programmme needs.
 */
final readonly class Host
{
    private function __construct(
        public ?int $id,
        public string $name,
        public ?string $bio,
        public ?string $photoPath,
        public bool $isActive,
    ) {}

    public static function fromValues(
        ?int $id,
        string $name,
        ?string $bio = null,
        ?string $photoPath = null,
        bool $isActive = true,
    ): self {
        $name = trim($name);
        $bio = trim((string) $bio);
        $photoPath = trim((string) $photoPath);

        if ($name === '') {
            throw new InvalidArgumentException('A host needs a name.');
        }

        return new self(
            id: $id,
            name: $name,
            bio: $bio !== '' ? $bio : null,
            photoPath: $photoPath !== '' ? $photoPath : null,
            isActive: $isActive,
        );
    }
}
