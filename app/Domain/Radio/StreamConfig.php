<?php

namespace App\Domain\Radio;

final readonly class StreamConfig
{
    private function __construct(
        public ?string $url,
        public StreamFormat $format,
        public ?string $name,
    ) {}

    public static function fromValues(?string $url, ?string $format, ?string $name, bool $allowHttp = false): self
    {
        $url = trim((string) $url);
        $name = trim((string) $name);

        return new self(
            url: self::isPlayableUrl($url, $allowHttp) ? $url : null,
            format: StreamFormat::fromString($format),
            name: $name !== '' ? $name : null,
        );
    }

    public function isConfigured(): bool
    {
        return $this->url !== null;
    }

    public function mimeType(): string
    {
        return $this->format->mimeType();
    }

    public function requiresHlsSupport(): bool
    {
        return $this->format->isHls();
    }

    private static function isPlayableUrl(string $url, bool $allowHttp): bool
    {
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = mb_strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if ($scheme === 'https') {
            return true;
        }

        return $allowHttp && $scheme === 'http';
    }
}
