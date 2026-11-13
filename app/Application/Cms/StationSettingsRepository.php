<?php

namespace App\Application\Cms;

use App\Domain\Cms\StationSettings;

interface StationSettingsRepository
{
    /**
     * The stored settings, or null when nothing has been saved yet (or the
     * storage is not reachable). Callers must be able to work without a row.
     */
    public function current(): ?StationSettings;

    public function save(StationSettings $settings): StationSettings;
}
