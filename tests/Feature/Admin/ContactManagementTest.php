<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\AdminUser;
use App\Models\SocialLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContactManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();

        config([
            'radio.stream.url' => null,
            'radio.stream.name' => null,
            'radio.contact.email' => null,
            'radio.social' => [],
        ]);
    }

    public function test_an_administrator_can_save_the_contact_details(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contacto', [
                'email' => 'contacto@radio.test',
                'phone' => '+54 11 5555 1234',
            ])
            ->assertRedirect(route('admin.contact.edit'))
            ->assertSessionHas('status', 'Datos de contacto guardados. Los enlaces sociales se administran por separado.');

        $this->assertDatabaseHas('contact_settings', [
            'email' => 'contacto@radio.test',
            'phone' => '+54 11 5555 1234',
        ]);

        $this->assertSame('contact.updated', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_saved_contact_details_appear_on_the_public_page(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contacto', [
                'email' => 'contacto@radio.test',
                'phone' => '+54 11 5555 1234',
            ])
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('contacto@radio.test')
            ->assertSee('tel:+541155551234', false)
            ->assertSee('+54 11 5555 1234');
    }

    public function test_the_contact_form_rejects_an_invalid_phone(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contacto', [
                'email' => 'contacto@radio.test',
                'phone' => 'llamame',
            ])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('contact_settings', 0);
    }

    public function test_stored_contact_details_override_the_environment_values(): void
    {
        config(['radio.contact.email' => 'entorno@radio.test']);

        $this->get('/')
            ->assertOk()
            ->assertSee('entorno@radio.test');

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contacto', ['email' => 'panel@radio.test'])
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('panel@radio.test')
            ->assertDontSee('entorno@radio.test');
    }

    public function test_an_administrator_can_add_an_https_social_link(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/contacto/redes', [
                'label' => 'Instagram',
                'url' => 'https://instagram.com/emisora',
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.contact.edit'))
            ->assertSessionHas('status', 'Enlace social agregado.');

        $this->assertDatabaseHas('social_links', [
            'label' => 'Instagram',
            'url' => 'https://instagram.com/emisora',
            'is_active' => true,
        ]);

        $this->assertSame('social.created', AdminActivity::query()->latest('id')->firstOrFail()->action);

        $this->get('/')
            ->assertOk()
            ->assertSee('Instagram')
            ->assertSee('https://instagram.com/emisora', false);
    }

    public function test_a_social_link_must_use_https(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/contacto/redes', [
                'label' => 'Sitio',
                'url' => 'http://example.com/emisora',
            ])
            ->assertSessionHasErrors('url');

        $this->assertDatabaseCount('social_links', 0);
    }

    public function test_inactive_social_links_are_not_published(): void
    {
        SocialLink::query()->create([
            'label' => 'Canal inactivo',
            'url' => 'https://example.com/inactivo',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Canal inactivo')
            ->assertDontSee('https://example.com/inactivo', false);
    }

    public function test_an_administrator_can_update_and_delete_a_social_link(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/contacto/redes', [
                'label' => 'Instagram',
                'url' => 'https://instagram.com/emisora',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $link = SocialLink::query()->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/contacto/redes/'.$link->id, [
                'label' => 'Instagram oficial',
                'url' => 'https://instagram.com/emisora-oficial',
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Enlace social actualizado.');

        $this->assertDatabaseHas('social_links', [
            'id' => $link->id,
            'label' => 'Instagram oficial',
            'url' => 'https://instagram.com/emisora-oficial',
        ]);
        $this->assertSame('social.updated', AdminActivity::query()->latest('id')->firstOrFail()->action);

        $this->actingAs($this->admin, 'admin')
            ->delete('/admin/contacto/redes/'.$link->id)
            ->assertRedirect()
            ->assertSessionHas('status', 'Enlace social eliminado.');

        $this->assertDatabaseCount('social_links', 0);
        $this->assertSame('social.deleted', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_a_social_link_falls_back_to_the_environment_configuration(): void
    {
        config(['radio.social' => ['Facebook' => 'https://facebook.com/emisora']]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Facebook')
            ->assertSee('https://facebook.com/emisora', false);
    }
}
