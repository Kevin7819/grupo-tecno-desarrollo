<?php

namespace App\Application\Cms;

use App\Domain\Cms\StationSettings;

/**
 * Resolved station settings for rendering: database first, environment
 * variables as the fallback for the stream, always a value object.
 */
interface StationSettingsProvider
{
    public function current(): StationSettings;
}
