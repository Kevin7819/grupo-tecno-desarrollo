<?php

namespace App\Infrastructure\Radio;

use App\Application\Cms\StationSettingsProvider;
use App\Application\Radio\StreamConfigProvider;
use App\Domain\Radio\StreamConfig;

/**
 * The player keeps consuming `StreamConfigProvider`; the value now comes from
 * the station settings, which already merge the database and RADIO_*.
 */
final class ConfiguredStreamConfigProvider implements StreamConfigProvider
{
    public function __construct(private readonly StationSettingsProvider $settings) {}

    public function current(): StreamConfig
    {
        return $this->settings->current()->stream;
    }
}
