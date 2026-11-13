<?php

namespace App\Domain\Cms;

use InvalidArgumentException;

/**
 * One weekly slot of a program: a weekday plus a time range.
 *
 * A program that airs several days per week owns several slots; a program
 * that crosses midnight is stored as two slots, one ending at 23:59 and one
 * starting at 00:00 on the next weekday.
 */
final readonly class ScheduleSlot
{
    private function __construct(
        public Weekday $weekday,
        public string $startsAt,
        public string $endsAt,
    ) {}

    public static function fromValues(int $weekday, string $startsAt, string $endsAt): self
    {
        $slot = new self(
            weekday: Weekday::fromNumber($weekday),
            startsAt: self::normalizeTime($startsAt),
            endsAt: self::normalizeTime($endsAt),
        );

        if ($slot->endsAtMinutes() <= $slot->startsAtMinutes()) {
            throw new InvalidArgumentException("Slot end [{$slot->endsAt}] must be after its start [{$slot->startsAt}].");
        }

        return $slot;
    }

    public function startsAtMinutes(): int
    {
        return self::minutes($this->startsAt);
    }

    public function endsAtMinutes(): int
    {
        return self::minutes($this->endsAt);
    }

    public function durationInMinutes(): int
    {
        return $this->endsAtMinutes() - $this->startsAtMinutes();
    }

    public function overlaps(self $other): bool
    {
        if ($this->weekday !== $other->weekday) {
            return false;
        }

        return $this->startsAtMinutes() < $other->endsAtMinutes()
            && $other->startsAtMinutes() < $this->endsAtMinutes();
    }

    public function isSameSlotAs(self $other): bool
    {
        return $this->weekday === $other->weekday
            && $this->startsAt === $other->startsAt;
    }

    public function timeRange(): string
    {
        return $this->startsAt.' – '.$this->endsAt;
    }

    public function label(): string
    {
        return $this->weekday->label().' · '.$this->timeRange();
    }

    private static function minutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return $hours * 60 + $minutes;
    }

    private static function normalizeTime(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $value, $matches) !== 1) {
            throw new InvalidArgumentException("Invalid time [{$value}].");
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        if ($hours > 23 || $minutes > 59) {
            throw new InvalidArgumentException("Invalid time [{$value}].");
        }

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
