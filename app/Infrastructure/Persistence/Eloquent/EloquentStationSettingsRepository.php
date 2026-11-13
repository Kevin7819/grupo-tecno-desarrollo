<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\StationSettingsRepository;
use App\Domain\Cms\StationSettings;
use App\Models\StationSetting;
use Illuminate\Database\QueryException;

final class EloquentStationSettingsRepository implements StationSettingsRepository
{
    private const SETTINGS_ID = 1;

    public function current(): ?StationSettings
    {
        try {
            $row = StationSetting::query()->orderBy('id')->first();
        } catch (QueryException) {
            // The settings table does not exist yet (fresh clone before
            // `php artisan migrate`). The public site must keep working.
            return null;
        }

        if ($row === null) {
            return null;
        }

        return StationSettings::fromValues(
            stationName: $row->station_name,
            slogan: $row->slogan,
            description: $row->description,
            logoPath: $row->logo_path,
            streamUrl: $row->stream_url,
            streamFormat: $row->stream_format,
            streamName: $row->stream_name,
            status: $row->status,
            allowHttp: app()->environment('local'),
        );
    }

    public function save(StationSettings $settings): StationSettings
    {
        StationSetting::query()->updateOrCreate(
            ['id' => self::SETTINGS_ID],
            [
                'station_name' => $settings->stationName,
                'slogan' => $settings->slogan,
                'description' => $settings->description,
                'logo_path' => $settings->logoPath,
                'stream_url' => $settings->stream->url,
                'stream_format' => $settings->stream->format->value,
                'stream_name' => $settings->stream->name,
                'status' => $settings->status->value,
            ],
        );

        return $settings;
    }
}
