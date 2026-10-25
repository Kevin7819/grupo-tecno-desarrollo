<?php

namespace Tests\Unit\Domain;

use App\Domain\Radio\StreamConfig;
use App\Domain\Radio\StreamFormat;
use PHPUnit\Framework\TestCase;

final class StreamConfigTest extends TestCase
{
    public function test_empty_url_is_not_configured(): void
    {
        $config = StreamConfig::fromValues('', 'mp3', 'Señal');

        $this->assertFalse($config->isConfigured());
        $this->assertNull($config->url);
    }

    public function test_whitespace_url_is_not_configured(): void
    {
        $config = StreamConfig::fromValues('   ', 'mp3', 'Señal');

        $this->assertFalse($config->isConfigured());
    }

    public function test_invalid_url_is_not_configured(): void
    {
        $config = StreamConfig::fromValues('not a url', 'mp3', null);

        $this->assertFalse($config->isConfigured());
        $this->assertNull($config->url);
    }

    public function test_non_http_schemes_are_not_configured(): void
    {
        foreach (['javascript:alert(1)', 'ftp://example.com/live.mp3', 'file:///etc/passwd'] as $url) {
            $config = StreamConfig::fromValues($url, 'mp3', null);

            $this->assertFalse($config->isConfigured(), "Expected [{$url}] to be rejected.");
        }
    }

    public function test_https_url_is_configured(): void
    {
        $config = StreamConfig::fromValues('https://stream.example.com/live.mp3', 'mp3', 'Señal');

        $this->assertTrue($config->isConfigured());
        $this->assertSame('https://stream.example.com/live.mp3', $config->url);
    }

    public function test_unknown_format_falls_back_to_mp3(): void
    {
        $config = StreamConfig::fromValues('https://stream.example.com/live', 'ogg', null);

        $this->assertSame(StreamFormat::Mp3, $config->format);
        $this->assertSame('audio/mpeg', $config->mimeType());
    }

    public function test_format_is_case_insensitive(): void
    {
        $config = StreamConfig::fromValues('https://stream.example.com/live', 'HLS', null);

        $this->assertSame(StreamFormat::Hls, $config->format);
        $this->assertTrue($config->requiresHlsSupport());
    }

    public function test_blank_name_becomes_null(): void
    {
        $config = StreamConfig::fromValues('https://stream.example.com/live', 'mp3', '   ');

        $this->assertNull($config->name);
    }

    public function test_name_is_trimmed(): void
    {
        $config = StreamConfig::fromValues('https://stream.example.com/live', 'mp3', '  Señal principal  ');

        $this->assertSame('Señal principal', $config->name);
    }

    public function test_known_formats_expose_their_mime_types(): void
    {
        $this->assertSame('audio/mpeg', StreamFormat::Mp3->mimeType());
        $this->assertSame('audio/aac', StreamFormat::Aac->mimeType());
        $this->assertSame('application/vnd.apple.mpegurl', StreamFormat::Hls->mimeType());
    }
}
