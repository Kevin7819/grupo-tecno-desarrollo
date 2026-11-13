<?php

namespace App\Domain\Cms;

/**
 * Whether the station is broadcasting. A station that is not active keeps
 * its programmme and settings, but the public player is not offered.
 */
enum StationStatus: string
{
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';

    public static function fromString(?string $value): self
    {
        return self::tryFrom(mb_strtolower(trim((string) $value))) ?? self::Active;
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Maintenance => 'En mantenimiento',
            self::Inactive => 'Inactiva',
        };
    }

    /**
     * Public explanation shown instead of the player when the station is not
     * broadcasting. It never claims an on-air signal.
     */
    public function publicNotice(): string
    {
        return match ($this) {
            self::Active => '',
            self::Maintenance => 'La emisora está en mantenimiento. La transmisión vuelve en cuanto termine la intervención técnica.',
            self::Inactive => 'La emisora no está transmitiendo en este momento.',
        };
    }
}
