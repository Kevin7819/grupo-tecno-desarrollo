<?php

namespace App\Application\Cms;

use App\Domain\Cms\StationStatus;

final readonly class StationSettingsInput
{
    public function __construct(
        public ?string $stationName,
        public ?string $slogan,
        public ?string $description,
        public ?string $logoPath,
        public ?string $streamUrl,
        public string $streamFormat,
        public ?string $streamName,
        public StationStatus $status,
    ) {}
}
