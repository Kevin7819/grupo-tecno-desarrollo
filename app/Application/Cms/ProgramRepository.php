<?php

namespace App\Application\Cms;

use App\Domain\Cms\Program;
use App\Domain\Cms\ProgramStatus;

interface ProgramRepository
{
    /**
     * @return Program[]
     */
    public function all(): array;

    /**
     * Programs visible on the public site, already ordered.
     *
     * @return Program[]
     */
    public function published(): array;

    public function find(int $id): ?Program;

    public function create(ProgramData $data): Program;

    public function update(Program $program, ProgramData $data): Program;

    public function updateStatus(Program $program, ProgramStatus $status): Program;

    public function delete(Program $program): void;

    public function countByStatus(ProgramStatus $status): int;
}
