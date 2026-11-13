<?php

namespace App\Application\Cms;

use App\Domain\Cms\Host;

final class DeleteHost
{
    public function __construct(
        private readonly HostRepository $hosts,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(Host $host, AdminActor $actor): void
    {
        $this->hosts->delete($host);
        $this->activity->record($actor, 'host.deleted', $host->name);
    }
}
