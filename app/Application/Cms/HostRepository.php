<?php

namespace App\Application\Cms;

use App\Domain\Cms\Host;

interface HostRepository
{
    /**
     * @return Host[]
     */
    public function all(): array;

    /**
     * @return Host[]
     */
    public function active(): array;

    public function find(int $id): ?Host;

    public function create(HostInput $input): Host;

    public function update(Host $host, HostInput $input): Host;

    public function delete(Host $host): void;

    public function countActive(): int;
}
