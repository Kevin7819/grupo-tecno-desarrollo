<?php

namespace App\Application\Cms;

use App\Domain\Cms\InvalidSchedule;
use App\Domain\Cms\Program;
use App\Domain\Cms\ScheduleSlot;

final class SaveProgram
{
    public function __construct(
        private readonly ProgramRepository $programs,
        private readonly AdminActivityLogger $activity,
    ) {}

    /**
     * @throws InvalidSchedule
     * @throws ScheduleConflict
     */
    public function handle(?Program $existing, ProgramInput $input, AdminActor $actor): Program
    {
        $data = $input->toData();

        $this->assertNoConflict($data->schedule, $existing);

        if ($existing === null) {
            $program = $this->programs->create($data);
            $this->activity->record($actor, 'program.created', $program->name);

            return $program;
        }

        $program = $this->programs->update($existing, $data);
        $this->activity->record($actor, 'program.updated', $program->name);

        return $program;
    }

    /**
     * A slot may not overlap any other program's slot on the same weekday.
     * Drafts are checked too: otherwise publishing would fail later without
     * an obvious reason.
     *
     * @param  ScheduleSlot[]  $schedule
     *
     * @throws ScheduleConflict
     */
    private function assertNoConflict(array $schedule, ?Program $existing): void
    {
        foreach ($this->programs->all() as $other) {
            if ($other->id !== null && $other->id === $existing?->id) {
                continue;
            }

            foreach ($other->schedule as $otherSlot) {
                foreach ($schedule as $slot) {
                    if ($slot->overlaps($otherSlot)) {
                        throw ScheduleConflict::between($other, $otherSlot);
                    }
                }
            }
        }
    }
}
