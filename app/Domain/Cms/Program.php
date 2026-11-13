<?php

namespace App\Domain\Cms;

use InvalidArgumentException;

/**
 * A radio program with its weekly slots and its hosts.
 */
final readonly class Program
{
    /**
     * @param  ScheduleSlot[]  $schedule
     * @param  Host[]  $hosts
     */
    private function __construct(
        public ?int $id,
        public string $name,
        public ?string $description,
        public ?string $coverPath,
        public ProgramStatus $status,
        public int $sortOrder,
        public array $schedule,
        public array $hosts,
    ) {}

    /**
     * @param  ScheduleSlot[]  $schedule
     * @param  Host[]  $hosts
     */
    public static function fromValues(
        ?int $id,
        string $name,
        ?string $description = null,
        ?string $coverPath = null,
        ?ProgramStatus $status = null,
        int $sortOrder = 0,
        array $schedule = [],
        array $hosts = [],
    ): self {
        $name = trim($name);
        $description = trim((string) $description);
        $coverPath = trim((string) $coverPath);

        if ($name === '') {
            throw new InvalidArgumentException('A program needs a name.');
        }

        $schedule = array_values($schedule);
        usort($schedule, static fn (ScheduleSlot $a, ScheduleSlot $b): int => [$a->weekday->value, $a->startsAt] <=> [$b->weekday->value, $b->startsAt]);

        return new self(
            id: $id,
            name: $name,
            description: $description !== '' ? $description : null,
            coverPath: $coverPath !== '' ? $coverPath : null,
            status: $status ?? ProgramStatus::Draft,
            sortOrder: max(0, $sortOrder),
            schedule: $schedule,
            hosts: array_values($hosts),
        );
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status->isPubliclyVisible();
    }

    public function hasSchedule(): bool
    {
        return $this->schedule !== [];
    }

    /**
     * First slot of the week, used as a compact summary in lists.
     */
    public function firstSlot(): ?ScheduleSlot
    {
        return $this->schedule[0] ?? null;
    }

    /**
     * @return int[]
     */
    public function hostIds(): array
    {
        return array_values(array_filter(
            array_map(static fn (Host $host): ?int => $host->id, $this->hosts),
            static fn (?int $id): bool => $id !== null,
        ));
    }

    /**
     * @return string[]
     */
    public function hostNames(): array
    {
        return array_map(static fn (Host $host): string => $host->name, $this->hosts);
    }
}
