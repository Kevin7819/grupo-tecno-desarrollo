<?php

namespace Tests\Unit;

use App\Domain\Radio\ContactChannels;
use PHPUnit\Framework\TestCase;

final class ContactChannelsTest extends TestCase
{
    public function test_it_keeps_valid_https_links_and_email(): void
    {
        $channels = ContactChannels::fromValues('hola@example.com', [
            'Instagram' => 'https://instagram.com/ejemplo',
        ]);

        $this->assertTrue($channels->hasEmail());
        $this->assertTrue($channels->hasSocial());
        $this->assertFalse($channels->isEmpty());
        $this->assertSame('mailto:hola@example.com', $channels->mailto());
        $this->assertSame(['Instagram' => 'https://instagram.com/ejemplo'], $channels->social);
    }

    public function test_it_rejects_non_https_links(): void
    {
        $channels = ContactChannels::fromValues(null, [
            'Instagram' => 'http://instagram.com/ejemplo',
            'TikTok' => 'javascript:alert(1)',
            'Facebook' => 'ftp://example.com',
            'YouTube' => 'no-es-una-url',
        ]);

        $this->assertFalse($channels->hasSocial());
        $this->assertTrue($channels->isEmpty());
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $channels = ContactChannels::fromValues('no-es-un-correo', []);

        $this->assertFalse($channels->hasEmail());
        $this->assertNull($channels->mailto());
    }

    public function test_empty_configuration_is_empty(): void
    {
        $channels = ContactChannels::fromValues('  ', []);

        $this->assertTrue($channels->isEmpty());
        $this->assertSame([], $channels->social);
    }
}
