<?php

namespace App\Domain\Cms;

use RuntimeException;

/**
 * The weekly schedule of one program is inconsistent on its own: a slot ends
 * before it starts, or two slots of the same program overlap.
 */
final class InvalidSchedule extends RuntimeException
{
    public static function duplicateSlot(ScheduleSlot $slot): self
    {
        return new self("The program already has a slot on {$slot->label()}.");
    }

    public static function overlappingSlot(ScheduleSlot $a, ScheduleSlot $b): self
    {
        return new self("Slots {$a->label()} and {$b->label()} overlap.");
    }
}
