<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\AdminUser;
use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\ImageFixtures;
use Tests\TestCase;

final class GalleryManagementTest extends TestCase
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

    public function test_an_administrator_can_upload_a_published_image(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/galeria', [
                'title' => 'Cabina de prueba',
                'alt_text' => 'Cabina vacía de prueba',
                'caption' => 'Dato de prueba',
                'sort_order' => 2,
                'is_published' => '1',
                'image' => ImageFixtures::png('cabina.png'),
            ])
            ->assertRedirect(route('admin.gallery.index'))
            ->assertSessionHas('status', 'Imagen agregada a la galería.');

        $image = GalleryImage::query()->firstOrFail();

        $this->assertSame('Cabina de prueba', $image->title);
        $this->assertSame('Cabina vacía de prueba', $image->alt_text);
        $this->assertSame(2, $image->sort_order);
        $this->assertTrue($image->is_published);
        Storage::disk('public')->assertExists($image->image_path);
        $this->assertSame('gallery.created', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_published_images_appear_on_the_public_page_and_unpublished_ones_do_not(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/galeria', [
                'title' => 'Imagen publicada',
                'alt_text' => 'Texto alternativo publicado',
                'is_published' => '1',
                'image' => ImageFixtures::png(),
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->post('/admin/galeria', [
                'title' => 'Imagen oculta',
                'alt_text' => 'Texto alternativo oculto',
                'image' => ImageFixtures::png(),
            ])
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Imagen publicada')
            ->assertSee('Texto alternativo publicado')
            ->assertDontSee('Imagen oculta')
            ->assertDontSee('Texto alternativo oculto');
    }

    public function test_an_administrator_can_update_an_image_without_replacing_the_file(): void
    {
        $path = $this->uploadImage('Título original');

        $image = GalleryImage::query()->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/galeria/'.$image->id, [
                'title' => 'Título actualizado',
                'alt_text' => 'Texto alternativo actualizado',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.gallery.index'))
            ->assertSessionHas('status', 'Imagen actualizada.');

        $image->refresh();

        $this->assertSame('Título actualizado', $image->title);
        $this->assertSame($path, $image->image_path);
        Storage::disk('public')->assertExists($path);
        $this->assertSame('gallery.updated', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_deleting_an_image_removes_the_record_and_the_file(): void
    {
        $path = $this->uploadImage('Imagen a eliminar');

        $image = GalleryImage::query()->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/galeria/'.$image->id.'/eliminar')
            ->assertOk()
            ->assertSee('Imagen a eliminar');

        $this->actingAs($this->admin, 'admin')
            ->delete('/admin/galeria/'.$image->id)
            ->assertRedirect(route('admin.gallery.index'))
            ->assertSessionHas('status', 'Imagen eliminada.');

        $this->assertDatabaseCount('gallery_images', 0);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame('gallery.deleted', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_the_form_rejects_files_that_are_not_images(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/galeria', [
                'title' => 'Archivo inválido',
                'alt_text' => 'No es una imagen',
                'image' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('gallery_images', 0);
    }

    public function test_the_form_requires_a_title_and_alternative_text(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/galeria', [
                'image' => ImageFixtures::png(),
            ])
            ->assertSessionHasErrors(['title', 'alt_text']);

        $this->assertDatabaseCount('gallery_images', 0);
    }

    private function uploadImage(string $title): string
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/galeria', [
                'title' => $title,
                'alt_text' => 'Texto alternativo de prueba',
                'is_published' => '1',
                'image' => ImageFixtures::png(),
            ])
            ->assertRedirect(route('admin.gallery.index'));

        return GalleryImage::query()->firstOrFail()->image_path;
    }
}
