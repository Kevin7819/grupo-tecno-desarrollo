<?php

namespace App\Domain\Cms;

use InvalidArgumentException;

/**
 * Day of the week using the ISO-8601 numbering (Monday = 1, Sunday = 7).
 */
enum Weekday: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public static function fromNumber(int $number): self
    {
        return self::tryFrom($number) ?? throw new InvalidArgumentException("Unknown weekday [{$number}].");
    }

    public function label(): string
    {
        return match ($this) {
            self::Monday => 'Lunes',
            self::Tuesday => 'Martes',
            self::Wednesday => 'Miércoles',
            self::Thursday => 'Jueves',
            self::Friday => 'Viernes',
            self::Saturday => 'Sábado',
            self::Sunday => 'Domingo',
        };
    }

    /**
     * Days between this weekday and the given one, both included in the
     * weekly cycle. The result is 0 when both are the same day.
     */
    public function daysUntil(self $target): int
    {
        return ($target->value - $this->value + 7) % 7;
    }
}
