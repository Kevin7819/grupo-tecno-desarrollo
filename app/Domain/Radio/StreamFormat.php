<?php

namespace App\Domain\Radio;

enum StreamFormat: string
{
    case Mp3 = 'mp3';
    case Aac = 'aac';
    case Hls = 'hls';

    public static function fromString(?string $value): self
    {
        return self::tryFrom(mb_strtolower(trim((string) $value))) ?? self::Mp3;
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::Mp3 => 'audio/mpeg',
            self::Aac => 'audio/aac',
            self::Hls => 'application/vnd.apple.mpegurl',
        };
    }

    public function isHls(): bool
    {
        return $this === self::Hls;
    }
}
