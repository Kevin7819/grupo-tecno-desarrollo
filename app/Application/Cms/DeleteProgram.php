<?php

namespace App\Application\Cms;

use App\Domain\Cms\Program;

final class DeleteProgram
{
    public function __construct(
        private readonly ProgramRepository $programs,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(Program $program, AdminActor $actor): void
    {
        $this->programs->delete($program);
        $this->activity->record($actor, 'program.deleted', $program->name);
    }
}
