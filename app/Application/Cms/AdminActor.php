<?php

namespace App\Application\Cms;

/**
 * The administrator that performs a change. The activity log stores a
 * snapshot of the name so the history stays readable if the account is
 * removed later.
 */
final readonly class AdminActor
{
    public function __construct(
        public ?int $id,
        public string $name,
    ) {}

    public static function fromAdmin(?object $admin): self
    {
        return new self(
            id: $admin?->getKey(),
            name: trim((string) ($admin->name ?? '')) ?: 'Administrador',
        );
    }
}
