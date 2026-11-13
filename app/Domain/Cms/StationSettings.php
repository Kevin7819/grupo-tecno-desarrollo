<?php

namespace App\Domain\Cms;

use App\Domain\Radio\StreamConfig;

/**
 * Station identity and broadcast configuration.
 *
 * Every text field is optional: an empty value means "not configured yet"
 * and the public site keeps its neutral placeholder. The stream configuration
 * is always present as a value object, which may itself report
 * `isConfigured() === false`.
 */
final readonly class StationSettings
{
    private function __construct(
        public ?string $stationName,
        public ?string $slogan,
        public ?string $description,
        public ?string $logoPath,
        public StreamConfig $stream,
        public StationStatus $status,
    ) {}

    public static function fromValues(
        ?string $stationName,
        ?string $slogan,
        ?string $description,
        ?string $logoPath,
        ?string $streamUrl,
        ?string $streamFormat,
        ?string $streamName,
        ?string $status,
        bool $allowHttp = false,
    ): self {
        return new self(
            stationName: self::text($stationName),
            slogan: self::text($slogan),
            description: self::text($description),
            logoPath: self::text($logoPath),
            stream: StreamConfig::fromValues($streamUrl, $streamFormat, $streamName, $allowHttp),
            status: StationStatus::fromString($status),
        );
    }

    /**
     * Settings that exist only as configuration, without a stored row.
     */
    public static function empty(StreamConfig $stream): self
    {
        return new self(
            stationName: null,
            slogan: null,
            description: null,
            logoPath: null,
            stream: $stream,
            status: StationStatus::Active,
        );
    }

    public function withStream(StreamConfig $stream): self
    {
        return new self(
            stationName: $this->stationName,
            slogan: $this->slogan,
            description: $this->description,
            logoPath: $this->logoPath,
            stream: $stream,
            status: $this->status,
        );
    }

    public function hasIdentity(): bool
    {
        return $this->stationName !== null
            || $this->slogan !== null
            || $this->description !== null
            || $this->logoPath !== null;
    }

    public function isBroadcasting(): bool
    {
        return $this->status->isActive() && $this->stream->isConfigured();
    }

    private static function text(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
