<?php

namespace Tests\Feature\Admin;

use App\Domain\Cms\SiteContent;
use App\Models\AdminActivity;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SiteContentManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();
        config(['radio.stream.url' => null, 'radio.stream.name' => null]);
    }

    /**
     * @return array<string, string>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'hero_kicker' => 'Radio por Internet',
            'hero_title' => 'Título de prueba',
            'hero_intro' => 'Introducción de prueba para la portada.',
            'about_title' => 'Sobre la emisora de prueba',
            'about_text' => 'Texto de presentación de prueba.',
            'gallery_title' => 'Galería de prueba',
            'gallery_intro' => 'Introducción de la galería de prueba.',
            'contact_title' => 'Contacto de prueba',
            'contact_intro' => 'Introducción de contacto de prueba.',
        ], $overrides);
    }

    public function test_an_administrator_can_save_the_public_copy(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contenido', $this->payload())
            ->assertRedirect(route('admin.content.edit'))
            ->assertSessionHas('status', 'Contenido de la página pública guardado.');

        $this->assertDatabaseHas('site_contents', [
            'id' => 1,
            'hero_title' => 'Título de prueba',
            'about_text' => 'Texto de presentación de prueba.',
            'contact_intro' => 'Introducción de contacto de prueba.',
        ]);

        $this->assertSame('content.updated', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_the_public_page_renders_the_saved_copy(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contenido', $this->payload([
                'hero_title' => 'Título publicado',
                'contact_intro' => 'Escribinos cuando quieras.',
            ]))
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Título publicado')
            ->assertSee('Escribinos cuando quieras.');
    }

    public function test_the_public_page_keeps_its_default_copy_when_nothing_is_saved(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(SiteContent::DEFAULT_HERO_TITLE)
            ->assertSee(SiteContent::DEFAULT_ABOUT_TITLE);
    }

    public function test_saving_empty_values_restores_the_default_copy(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contenido', $this->payload())
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contenido', $this->payload([
                'hero_title' => '',
                'contact_intro' => '',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('site_contents', [
            'id' => 1,
            'hero_title' => null,
            'contact_intro' => null,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(SiteContent::DEFAULT_HERO_TITLE);
    }

    public function test_the_content_form_rejects_overlong_text(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contenido', $this->payload([
                'hero_title' => str_repeat('a', 161),
            ]))
            ->assertSessionHasErrors('hero_title');

        $this->assertDatabaseCount('site_contents', 0);
    }
}
