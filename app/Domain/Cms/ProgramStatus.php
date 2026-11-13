<?php

namespace App\Domain\Cms;

/**
 * Editorial state of a program.
 *
 * `Draft` and `Inactive` are both hidden from the public site; the
 * difference is editorial: a draft was never published, an inactive program
 * was published and was taken off the air without losing its data.
 */
enum ProgramStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Inactive = 'inactive';

    public static function fromString(?string $value): self
    {
        return self::tryFrom(mb_strtolower(trim((string) $value))) ?? self::Draft;
    }

    public function isPublished(): bool
    {
        return $this === self::Published;
    }

    public function isPubliclyVisible(): bool
    {
        return $this->isPublished();
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Published => 'Publicado',
            self::Inactive => 'Inactivo',
        };
    }
}
