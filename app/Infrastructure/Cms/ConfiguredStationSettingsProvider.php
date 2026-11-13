<?php

namespace App\Infrastructure\Cms;

use App\Application\Cms\StationSettingsProvider;
use App\Application\Cms\StationSettingsRepository;
use App\Domain\Cms\StationSettings;
use App\Domain\Radio\StreamConfig;

/**
 * Resolves the settings used by every view.
 *
 * Precedence, in this order:
 *   1. the value stored in the database, when the stream URL is playable;
 *   2. the RADIO_* environment configuration, field by field;
 *   3. "stream not configured", which renders an explicit public state.
 *
 * A stored value is re-validated on read, so an invalid URL never reaches the
 * player while a valid environment configuration still exists.
 */
final class ConfiguredStationSettingsProvider implements StationSettingsProvider
{
    public function __construct(private readonly StationSettingsRepository $settings) {}

    public function current(): StationSettings
    {
        $environment = $this->environmentStream();
        $stored = $this->settings->current();

        if ($stored === null) {
            return StationSettings::empty($environment);
        }

        if (! $stored->stream->isConfigured()) {
            return $stored->withStream($environment);
        }

        return $stored;
    }

    private function environmentStream(): StreamConfig
    {
        return StreamConfig::fromValues(
            url: config('radio.stream.url'),
            format: config('radio.stream.format'),
            name: config('radio.stream.name'),
            allowHttp: app()->environment('local'),
        );
    }
}
