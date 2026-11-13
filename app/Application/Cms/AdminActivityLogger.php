<?php

namespace App\Application\Cms;

interface AdminActivityLogger
{
    public function record(AdminActor $actor, string $action, string $subject): void;
}
