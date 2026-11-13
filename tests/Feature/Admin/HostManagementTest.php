<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\AdminUser;
use App\Models\Host;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\ImageFixtures;
use Tests\TestCase;

final class HostManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = AdminUser::factory()->create();
        config(['radio.stream.url' => null, 'radio.stream.name' => null]);
    }

    public function test_an_administrator_can_create_a_host(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/locutores', [
                'name' => 'Locutor de prueba',
                'bio' => 'Biografía de prueba.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.hosts.index'))
            ->assertSessionHas('status', 'Locutor creado.');

        $host = Host::query()->firstOrFail();

        $this->assertSame('Locutor de prueba', $host->name);
        $this->assertSame('Biografía de prueba.', $host->bio);
        $this->assertTrue($host->is_active);
        $this->assertSame('host.created', AdminActivity::query()->latest('id')->firstOrFail()->action);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/locutores')
            ->assertOk()
            ->assertSee('Locutor de prueba');
    }

    public function test_an_administrator_can_upload_a_host_photo(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/locutores', [
                'name' => 'Locutora con foto',
                'is_active' => '1',
                'photo' => ImageFixtures::png('locutora.png'),
            ])
            ->assertRedirect(route('admin.hosts.index'));

        $host = Host::query()->firstOrFail();

        $this->assertNotNull($host->photo_path);
        Storage::disk('public')->assertExists($host->photo_path);
    }

    public function test_an_administrator_can_update_a_host_and_remove_its_photo(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/locutores', [
                'name' => 'Locutor original',
                'is_active' => '1',
                'photo' => ImageFixtures::png(),
            ])
            ->assertRedirect();

        $host = Host::query()->firstOrFail();
        $path = (string) $host->photo_path;

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/locutores/'.$host->id, [
                'name' => 'Locutor actualizado',
                'bio' => 'Biografía nueva.',
                'remove_photo' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Locutor actualizado.');

        $host->refresh();

        $this->assertSame('Locutor actualizado', $host->name);
        $this->assertNull($host->photo_path);
        $this->assertFalse($host->is_active);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame('host.updated', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_deleting_a_host_removes_its_photo(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/locutores', [
                'name' => 'Locutor a eliminar',
                'is_active' => '1',
                'photo' => ImageFixtures::png(),
            ])
            ->assertRedirect();

        $host = Host::query()->firstOrFail();
        $path = (string) $host->photo_path;

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/locutores/'.$host->id.'/eliminar')
            ->assertOk()
            ->assertSee('Locutor a eliminar');

        $this->actingAs($this->admin, 'admin')
            ->delete('/admin/locutores/'.$host->id)
            ->assertRedirect(route('admin.hosts.index'))
            ->assertSessionHas('status', 'Locutor eliminado.');

        $this->assertDatabaseCount('hosts', 0);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame('host.deleted', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_the_host_form_rejects_a_file_that_is_not_an_image(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/locutores', [
                'name' => 'Locutor inválido',
                'photo' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseCount('hosts', 0);
    }

    public function test_the_host_form_requires_a_name(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/locutores', ['is_active' => '1'])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('hosts', 0);
    }
}
