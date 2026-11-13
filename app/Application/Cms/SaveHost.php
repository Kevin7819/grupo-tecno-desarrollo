<?php

namespace App\Application\Cms;

use App\Domain\Cms\Host;

final class SaveHost
{
    public function __construct(
        private readonly HostRepository $hosts,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(?Host $existing, HostInput $input, AdminActor $actor): Host
    {
        if ($existing === null) {
            $host = $this->hosts->create($input);
            $this->activity->record($actor, 'host.created', $host->name);

            return $host;
        }

        $host = $this->hosts->update($existing, $input);
        $this->activity->record($actor, 'host.updated', $host->name);

        return $host;
    }
}
