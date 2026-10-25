<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_available_to_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Grupo Tecno Desarrollo');
        $response->assertSee('Crear cuenta');
    }

    public function test_player_reports_an_unconfigured_stream_without_dead_controls(): void
    {
        config([
            'radio.stream.url' => null,
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => null,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Transmisión no configurada');
        $response->assertDontSee('data-player-toggle', false);
        $response->assertDontSee('data-player-audio', false);
    }

    public function test_player_renders_a_configured_stream(): void
    {
        config([
            'radio.stream.url' => 'https://stream.example.com/live.mp3',
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => 'Señal de prueba',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('https://stream.example.com/live.mp3', false);
        $response->assertSee('Señal de prueba');
        $response->assertSee('data-player-toggle', false);
        $response->assertDontSee('Transmisión no configurada');
    }

    public function test_invalid_stream_url_falls_back_to_the_unconfigured_state(): void
    {
        config([
            'radio.stream.url' => 'javascript:alert(1)',
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => null,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Transmisión no configurada');
        $response->assertDontSee('javascript:alert(1)', false);
    }

    public function test_an_http_stream_is_not_configured_outside_local(): void
    {
        config([
            'radio.stream.url' => 'http://stream.example.com/live.mp3',
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => 'Señal de prueba',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Transmisión no configurada');
        $response->assertDontSee('EN VIVO');
        $response->assertDontSee('data-stream-url', false);
    }

    public function test_a_local_http_stream_is_configured(): void
    {
        $this->app['env'] = 'local';

        config([
            'radio.stream.url' => 'http://stream.example.com/live.mp3',
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => 'Señal de prueba',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-stream-url="http://stream.example.com/live.mp3"', false);
        $response->assertSee('EN VIVO');
        $response->assertDontSee('Transmisión no configurada');
    }

    public function test_an_http_stream_is_not_advertised_when_the_csp_would_block_it(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://radio.example.test']);

        config([
            'radio.stream.url' => 'http://stream.example.com/live.mp3',
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => 'Señal de prueba',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('Content-Security-Policy');
        $this->assertStringContainsString(
            'media-src https:',
            (string) $response->headers->get('Content-Security-Policy'),
        );
        $response->assertSee('Transmisión no configurada');
        $response->assertDontSee('EN VIVO');
        $response->assertDontSee('http://stream.example.com/live.mp3', false);
    }

    public function test_an_https_stream_is_advertised_in_production(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://radio.example.test']);

        config([
            'radio.stream.url' => 'https://stream.example.com/live.mp3',
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => 'Señal de prueba',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('https://stream.example.com/live.mp3', false);
        $response->assertSee('EN VIVO');
        $response->assertDontSee('Transmisión no configurada');
    }

    public function test_security_headers_are_sent(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()',
        );
    }

    public function test_csp_is_not_sent_in_the_testing_environment(): void
    {
        $response = $this->get('/');

        $this->assertNull($response->headers->get('Content-Security-Policy'));
    }

    public function test_the_player_script_is_bundled_with_the_application_entry_point(): void
    {
        $entryPoint = (string) file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("import './player.js';", $entryPoint);
    }
}
