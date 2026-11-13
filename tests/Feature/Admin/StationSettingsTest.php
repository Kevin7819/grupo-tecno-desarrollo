<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\AdminUser;
use App\Models\StationSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\ImageFixtures;
use Tests\TestCase;

final class StationSettingsTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();

        config([
            'radio.stream.url' => null,
            'radio.stream.format' => 'mp3',
            'radio.stream.name' => null,
        ]);
    }

    public function test_an_administrator_can_save_the_station_settings(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/emisora', [
                'station_name' => 'Radio Prueba',
                'slogan' => 'Solo prueba',
                'description' => 'Descripción de prueba.',
                'stream_url' => 'https://stream.example.com/live.mp3',
                'stream_format' => 'mp3',
                'stream_name' => 'Señal de prueba',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHas('status', 'Configuración de la emisora guardada.');

        $row = StationSetting::query()->firstOrFail();

        $this->assertSame('Radio Prueba', $row->station_name);
        $this->assertSame('Solo prueba', $row->slogan);
        $this->assertSame('https://stream.example.com/live.mp3', $row->stream_url);
        $this->assertSame('Señal de prueba', $row->stream_name);
        $this->assertSame('active', $row->status);
    }

    public function test_the_form_shows_the_stored_values(): void
    {
        $this->saveSettings(['station_name' => 'Radio Guardada', 'slogan' => 'Eslogan guardado']);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/emisora')
            ->assertOk()
            ->assertSee('Radio Guardada')
            ->assertSee('Eslogan guardado');
    }

    public function test_the_stored_stream_wins_over_the_environment(): void
    {
        config(['radio.stream.url' => 'https://env.example.com/live.mp3', 'radio.stream.name' => 'Desde el entorno']);

        $this->saveSettings([
            'stream_url' => 'https://database.example.com/live.mp3',
            'stream_name' => 'Desde la base',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://database.example.com/live.mp3', false)
            ->assertSee('Desde la base')
            ->assertDontSee('https://env.example.com/live.mp3', false);
    }

    public function test_the_environment_is_used_when_the_database_has_no_playable_stream(): void
    {
        config(['radio.stream.url' => 'https://env.example.com/live.mp3', 'radio.stream.name' => 'Desde el entorno']);

        $this->saveSettings(['stream_url' => null]);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://env.example.com/live.mp3', false)
            ->assertSee('Desde el entorno');
    }

    public function test_a_stored_stream_is_used_as_a_whole(): void
    {
        config(['radio.stream.format' => 'aac', 'radio.stream.name' => 'Nombre del entorno']);

        $this->saveSettings([
            'stream_url' => 'https://database.example.com/live',
            'stream_name' => null,
            'stream_format' => 'mp3',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://database.example.com/live', false)
            ->assertSee('Transmisión sin nombre configurado')
            ->assertDontSee('Nombre del entorno');
    }

    public function test_a_plain_http_stream_is_rejected_outside_local(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from('/admin/emisora')
            ->put('/admin/emisora', [
                'stream_url' => 'http://insecure.example.com/live.mp3',
                'stream_format' => 'mp3',
                'status' => 'active',
            ])
            ->assertRedirect('/admin/emisora')
            ->assertSessionHasErrors('stream_url');

        $this->assertDatabaseCount('station_settings', 0);
    }

    public function test_an_invalid_format_is_rejected(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from('/admin/emisora')
            ->put('/admin/emisora', [
                'stream_url' => 'https://stream.example.com/live',
                'stream_format' => 'ogg',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('stream_format');
    }

    public function test_an_unknown_station_status_is_rejected(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from('/admin/emisora')
            ->put('/admin/emisora', [
                'stream_url' => 'https://stream.example.com/live',
                'stream_format' => 'mp3',
                'status' => 'exploded',
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_the_public_player_is_not_offered_while_the_station_is_in_maintenance(): void
    {
        $this->saveSettings([
            'stream_url' => 'https://stream.example.com/live.mp3',
            'status' => 'maintenance',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('data-stream-url', false)
            ->assertSee('La emisora está en mantenimiento');
    }

    public function test_the_public_site_shows_the_station_identity(): void
    {
        $this->saveSettings([
            'station_name' => 'Emisora de Prueba',
            'slogan' => 'Eslogan público',
            'description' => 'Descripción pública de la emisora.',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Emisora de Prueba')
            ->assertSee('Eslogan público')
            ->assertSee('Descripción pública de la emisora.');
    }

    public function test_the_public_site_keeps_its_placeholders_without_settings(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Grupo Tecno Desarrollo')
            ->assertSee('Radio por Internet');
    }

    public function test_a_logo_can_be_uploaded(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/emisora', [
                'station_name' => 'Radio Prueba',
                'logo' => ImageFixtures::png('logo.png'),
                'stream_format' => 'mp3',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $path = StationSetting::query()->firstOrFail()->logo_path;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertSee(Storage::disk('public')->url($path), false);
    }

    public function test_a_non_image_logo_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->from('/admin/emisora')
            ->put('/admin/emisora', [
                'logo' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
                'stream_format' => 'mp3',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('logo');

        $this->assertFalse(StationSetting::query()->exists());
    }

    public function test_a_settings_change_is_recorded_in_the_activity_log(): void
    {
        $this->saveSettings(['station_name' => 'Radio Prueba']);

        $activity = AdminActivity::query()->latest('id')->firstOrFail();

        $this->assertSame('settings.updated', $activity->action);
        $this->assertSame('Radio Prueba', $activity->subject);
        $this->assertSame($this->admin->id, $activity->admin_user_id);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function saveSettings(array $overrides = []): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/emisora', array_merge([
                'station_name' => 'Radio Prueba',
                'slogan' => null,
                'description' => null,
                'stream_url' => 'https://stream.example.com/live.mp3',
                'stream_format' => 'mp3',
                'stream_name' => 'Señal de prueba',
                'status' => 'active',
            ], $overrides))
            ->assertSessionHasNoErrors();
    }
}
