<?php

namespace App\Application\Cms;

final readonly class ActivityEntry
{
    public function __construct(
        public string $action,
        public string $subject,
        public string $actorName,
        public \DateTimeImmutable $happenedAt,
    ) {}
}
