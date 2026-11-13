<?php

namespace App\Application\Cms;

use App\Domain\Cms\Program;
use App\Domain\Cms\ScheduleSlot;

/**
 * One concrete upcoming broadcast: a program, its weekly slot and how far
 * away it is from the reference day.
 */
final readonly class ScheduleOccurrence
{
    public function __construct(
        public Program $program,
        public ScheduleSlot $slot,
        public int $daysAhead,
        public bool $isOnAir,
    ) {}
}
