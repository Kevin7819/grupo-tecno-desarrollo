<?php

namespace App\Application\Cms;

use App\Domain\Cms\InvalidSchedule;
use App\Domain\Cms\ProgramStatus;
use App\Domain\Cms\ScheduleRules;
use App\Domain\Cms\ScheduleSlot;

/**
 * Raw program input coming from the admin form. It is not trusted: the
 * schedule is converted into domain slots by `toData()`, which applies the
 * consistency rules.
 */
final readonly class ProgramInput
{
    /**
     * @param  array<int, array{weekday: int|string, starts_at: string, ends_at: string}>  $schedule
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

    /**
     * @throws InvalidSchedule
     */
    public function toData(): ProgramData
    {
        /** @var ScheduleSlot[] $slots */
        $slots = ScheduleRules::build($this->schedule);

        return new ProgramData(
            name: $this->name,
            description: $this->description,
            coverPath: $this->coverPath,
            status: $this->status,
            sortOrder: $this->sortOrder,
            schedule: $slots,
            hostIds: $this->hostIds,
        );
    }
}
