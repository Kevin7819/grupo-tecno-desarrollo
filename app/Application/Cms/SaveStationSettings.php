<?php

namespace App\Application\Cms;

use App\Domain\Cms\StationSettings;

final class SaveStationSettings
{
    public function __construct(
        private readonly StationSettingsRepository $settings,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(StationSettingsInput $input, AdminActor $actor, bool $allowHttp = false): StationSettings
    {
        $settings = StationSettings::fromValues(
            stationName: $input->stationName,
            slogan: $input->slogan,
            description: $input->description,
            logoPath: $input->logoPath,
            streamUrl: $input->streamUrl,
            streamFormat: $input->streamFormat,
            streamName: $input->streamName,
            status: $input->status->value,
            allowHttp: $allowHttp,
        );

        $saved = $this->settings->save($settings);

        $this->activity->record($actor, 'settings.updated', $saved->stationName ?? 'Configuración de la emisora');

        return $saved;
    }
}
