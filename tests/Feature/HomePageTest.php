<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class HomePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, string|null>  $social
     */
    private function configureStream(
        string $url = 'https://stream.example.com/live.mp3',
        ?string $name = 'Radio de prueba',
        array $social = [],
        ?string $email = null,
    ): void {
        config([
            'radio.stream.url' => $url,
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => $name,
            'radio.social' => $social,
            'radio.contact.email' => $email,
        ]);
    }

    private function configureWithoutStream(): void
    {
        $this->configureStream(url: '', name: null);
    }

    public function test_home_page_renders_every_main_section(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertOk();

        foreach (['inicio', 'nosotros', 'programacion', 'galeria', 'contacto', 'reproductor'] as $section) {
            $response->assertSee('id="'.$section.'"', false);
        }
    }

    public function test_hero_uses_the_approved_message_and_actions(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertSee('Tu música, tu momento');
        $response->assertSee('Escuchar ahora');
        $response->assertSee('Conocer la emisora');
    }

    public function test_navigation_links_resolve_to_existing_sections(): void
    {
        $this->configureStream();

        $response = $this->get('/');
        $home = route('home');

        foreach (['inicio', 'nosotros', 'programacion', 'contacto'] as $section) {
            $response->assertSee('href="'.$home.'#'.$section.'"', false);
            $response->assertSee('id="'.$section.'"', false);
        }

        $response->assertDontSee('href="#"', false);
    }

    public function test_header_offers_the_live_call_to_action_and_badge(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertSee('Escuchar en vivo');
        $response->assertSee('EN VIVO');
        $response->assertSee('data-player-start', false);
    }

    public function test_mobile_menu_is_an_accessible_disclosure(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertSee('data-menu-toggle', false);
        $response->assertSee('aria-controls="menu-movil"', false);
        $response->assertSee('aria-expanded="false"', false);
        $response->assertSee('id="menu-movil"', false);
    }

    public function test_programming_states_are_declared_explicitly(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertSee('Disponible actualmente');
        $response->assertSee('En preparación');
        $response->assertSee('Propuesta futura');
        $response->assertSee('Horarios, locutores, programas y eventos no están confirmados.');
    }

    public function test_gallery_is_presented_as_brand_illustration(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertSee('Logo.jpg', false);
        $response->assertSee('No son fotografías de');
    }

    public function test_social_channels_show_an_empty_state_without_configuration(): void
    {
        $this->configureStream(social: [], email: null);

        $response = $this->get('/');

        $response->assertSee('Todavía no hay cuentas oficiales publicadas.');
        $response->assertSee('Todavía no hay un correo de contacto publicado.');
        $response->assertDontSee('instagram.com');
        $response->assertDontSee('wa.me');
        $response->assertDontSee('mailto:');
    }

    public function test_valid_social_channels_and_email_are_published(): void
    {
        $this->configureStream(
            social: ['Instagram' => 'https://instagram.com/ejemplo'],
            email: 'hola@example.com',
        );

        $response = $this->get('/');

        $response->assertSee('href="https://instagram.com/ejemplo"', false);
        $response->assertSee('mailto:hola@example.com', false);
        $response->assertDontSee('Todavía no hay cuentas oficiales publicadas.');
    }

    public function test_insecure_channel_values_are_never_published(): void
    {
        $this->configureStream(
            social: [
                'Instagram' => 'http://insecure.example.com',
                'TikTok' => 'javascript:alert(1)',
            ],
            email: 'no-es-un-correo',
        );

        $response = $this->get('/');

        $response->assertDontSee('insecure.example.com');
        $response->assertDontSee('javascript:alert(1)');
        $response->assertDontSee('no-es-un-correo');
    }

    public function test_the_player_renders_a_single_audio_element_with_lazy_loading(): void
    {
        $this->configureStream();

        $response = $this->get('/');
        $content = (string) $response->getContent();

        $this->assertSame(1, substr_count($content, '<audio'));
        $response->assertSee('preload="none"', false);
        $response->assertDontSee('autoplay', false);
    }

    public function test_the_player_offers_share_and_a_decorative_visualizer(): void
    {
        $this->configureStream();

        $response = $this->get('/');

        $response->assertSee('data-player-share', false);
        $response->assertSee('data-share-fallback', false);
        $response->assertSee('player-visualizer', false);
        $response->assertSee('No mide la señal de audio.');
    }

    public function test_the_compact_player_shares_the_same_audio_element(): void
    {
        $this->configureStream();

        $response = $this->get('/');
        $content = (string) $response->getContent();

        $response->assertSee('data-mini-player', false);
        $response->assertSee('data-mini-toggle', false);
        $response->assertSee('data-mini-mute', false);
        $response->assertSee('aria-label="Reproductor compacto"', false);
        $this->assertSame(1, substr_count($content, '<audio'));
    }

    public function test_the_compact_player_is_not_rendered_without_a_stream(): void
    {
        $this->configureWithoutStream();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('data-mini-player', false);
        $response->assertSee('Transmisión no configurada');
    }
}
