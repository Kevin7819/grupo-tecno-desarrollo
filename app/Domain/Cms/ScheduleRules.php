<?php

namespace App\Domain\Cms;

/**
 * Rules that keep a weekly schedule consistent.
 */
final class ScheduleRules
{
    /**
     * Build the slots of one program from raw form input.
     *
     * @param  array<int, array{weekday: int|string, starts_at: string, ends_at: string}>  $rows
     * @return ScheduleSlot[]
     *
     * @throws InvalidSchedule
     */
    public static function build(array $rows): array
    {
        $slots = [];

        foreach ($rows as $row) {
            try {
                $slots[] = ScheduleSlot::fromValues(
                    (int) $row['weekday'],
                    (string) $row['starts_at'],
                    (string) $row['ends_at'],
                );
            } catch (\InvalidArgumentException $exception) {
                throw new InvalidSchedule($exception->getMessage(), previous: $exception);
            }
        }

        self::assertConsistent($slots);

        return $slots;
    }

    /**
     * @param  ScheduleSlot[]  $slots
     *
     * @throws InvalidSchedule
     */
    public static function assertConsistent(array $slots): void
    {
        $slots = array_values($slots);

        foreach ($slots as $index => $slot) {
            foreach (array_slice($slots, $index + 1) as $other) {
                if ($slot->isSameSlotAs($other)) {
                    throw InvalidSchedule::duplicateSlot($slot);
                }

                if ($slot->overlaps($other)) {
                    throw InvalidSchedule::overlappingSlot($slot, $other);
                }
            }
        }
    }
}
