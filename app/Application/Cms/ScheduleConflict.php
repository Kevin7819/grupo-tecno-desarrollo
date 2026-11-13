<?php

namespace App\Application\Cms;

use App\Domain\Cms\Program;
use App\Domain\Cms\ScheduleSlot;
use RuntimeException;

/**
 * Two programs or slots of the same day would air at the same time.
 */
final class ScheduleConflict extends RuntimeException
{
    private function __construct(
        public readonly string $conflictingProgram,
        public readonly string $conflictingSlot,
    ) {
        parent::__construct("El horario se solapa con el programa «{$conflictingProgram}» ({$conflictingSlot}).");
    }

    public static function between(Program $program, ScheduleSlot $slot): self
    {
        return new self($program->name, $slot->label());
    }
}
