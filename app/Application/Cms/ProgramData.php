<?php

namespace App\Application\Cms;

use App\Domain\Cms\ProgramStatus;
use App\Domain\Cms\ScheduleSlot;

/**
 * Validated program data ready to be persisted.
 */
final readonly class ProgramData
{
    /**
     * @param  ScheduleSlot[]  $schedule
     * @param  int[]  $hostIds
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $coverPath,
        public ProgramStatus $status,
        public int $sortOrder,
        public array $schedule,
        public array $hostIds,
    ) {}
}
