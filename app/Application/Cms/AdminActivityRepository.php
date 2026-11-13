<?php

namespace App\Application\Cms;

interface AdminActivityRepository
{
    /**
     * @return ActivityEntry[]
     */
    public function latest(int $limit): array;
}
