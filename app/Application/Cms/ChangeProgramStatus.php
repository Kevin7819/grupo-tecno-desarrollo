<?php

namespace App\Application\Cms;

use App\Domain\Cms\Program;
use App\Domain\Cms\ProgramStatus;

final class ChangeProgramStatus
{
    public function __construct(
        private readonly ProgramRepository $programs,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(Program $program, ProgramStatus $status, AdminActor $actor): Program
    {
        $updated = $this->programs->updateStatus($program, $status);

        $this->activity->record($actor, match ($status) {
            ProgramStatus::Published => 'program.published',
            ProgramStatus::Inactive => 'program.inactivated',
            ProgramStatus::Draft => 'program.drafted',
        }, $updated->name);

        return $updated;
    }
}
