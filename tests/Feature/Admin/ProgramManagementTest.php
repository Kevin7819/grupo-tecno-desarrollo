<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\AdminUser;
use App\Models\Host;
use App\Models\Program;
use App\Models\ProgramSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\ImageFixtures;
use Tests\TestCase;

final class ProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();
        config(['radio.stream.url' => null, 'radio.stream.name' => null]);
    }

    public function test_an_administrator_can_create_a_program_with_several_days(): void
    {
        $host = Host::query()->create(['name' => 'Locutor Uno', 'is_active' => true]);

        $this->actingAs($this->admin, 'admin')
            ->post('/admin/programacion', $this->payload([
                'name' => 'Programa Matinal',
                'description' => 'Datos de prueba.',
                'status' => 'published',
                'sort_order' => 3,
                'hosts' => [$host->id],
                'schedule' => [
                    ['weekday' => '1', 'starts_at' => '08:00', 'ends_at' => '10:00'],
                    ['weekday' => '3', 'starts_at' => '08:00', 'ends_at' => '10:00'],
                ],
            ]))
            ->assertRedirect(route('admin.programs.index'))
            ->assertSessionHas('status', 'Programa creado.');

        $program = Program::query()->firstOrFail();

        $this->assertSame('Programa Matinal', $program->name);
        $this->assertSame('published', $program->status);
        $this->assertSame(3, $program->sort_order);
        $this->assertSame(2, ProgramSchedule::query()->where('program_id', $program->id)->count());
        $this->assertDatabaseHas('program_host', ['program_id' => $program->id, 'host_id' => $host->id]);
        $this->assertSame('program.created', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_the_create_form_renders(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/programacion/nuevo')
            ->assertOk()
            ->assertSee('Nuevo programa')
            ->assertSee('Franjas horarias');
    }

    public function test_empty_schedule_rows_are_ignored(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/programacion', $this->payload([
                'schedule' => [
                    ['weekday' => '', 'starts_at' => '', 'ends_at' => ''],
                    ['weekday' => '2', 'starts_at' => '12:00', 'ends_at' => '13:00'],
                    ['weekday' => '', 'starts_at' => '', 'ends_at' => ''],
                ],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('program_schedules', 1);
    }

    public function test_a_slot_ending_before_it_starts_is_rejected(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from('/admin/programacion/nuevo')
            ->post('/admin/programacion', $this->payload([
                'schedule' => [['weekday' => '1', 'starts_at' => '10:00', 'ends_at' => '09:00']],
            ]))
            ->assertRedirect('/admin/programacion/nuevo')
            ->assertSessionHasErrors('schedule.0.ends_at');

        $this->assertDatabaseCount('programs', 0);
    }

    public function test_two_overlapping_programs_are_rejected(): void
    {
        $this->createProgram('Programa Uno', [['weekday' => '1', 'starts_at' => '08:00', 'ends_at' => '10:00']]);

        $this->actingAs($this->admin, 'admin')
            ->from('/admin/programacion/nuevo')
            ->post('/admin/programacion', $this->payload([
                'name' => 'Programa Dos',
                'schedule' => [['weekday' => '1', 'starts_at' => '09:00', 'ends_at' => '11:00']],
            ]))
            ->assertRedirect('/admin/programacion/nuevo')
            ->assertSessionHasErrors('schedule');

        $this->assertDatabaseMissing('programs', ['name' => 'Programa Dos']);
    }

    public function test_a_program_that_touches_but_does_not_overlap_is_accepted(): void
    {
        $this->createProgram('Programa Uno', [['weekday' => '1', 'starts_at' => '08:00', 'ends_at' => '10:00']]);

        $this->actingAs($this->admin, 'admin')
            ->post('/admin/programacion', $this->payload([
                'name' => 'Programa Dos',
                'schedule' => [['weekday' => '1', 'starts_at' => '10:00', 'ends_at' => '11:00']],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('programs', ['name' => 'Programa Dos']);
    }

    public function test_the_same_slot_twice_in_one_program_is_rejected(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from('/admin/programacion/nuevo')
            ->post('/admin/programacion', $this->payload([
                'schedule' => [
                    ['weekday' => '4', 'starts_at' => '09:00', 'ends_at' => '11:00'],
                    ['weekday' => '4', 'starts_at' => '09:00', 'ends_at' => '11:00'],
                ],
            ]))
            ->assertSessionHasErrors('schedule');
    }

    public function test_an_administrator_can_edit_a_program(): void
    {
        $program = $this->createProgram('Nombre Original', [['weekday' => '1', 'starts_at' => '08:00', 'ends_at' => '09:00']]);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/programacion/'.$program->id.'/editar')
            ->assertOk()
            ->assertSee('Nombre Original');

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/programacion/'.$program->id, $this->payload([
                'name' => 'Nombre Nuevo',
                'status' => 'published',
                'schedule' => [['weekday' => '5', 'starts_at' => '18:00', 'ends_at' => '20:00']],
            ]))
            ->assertRedirect(route('admin.programs.index'))
            ->assertSessionHas('status', 'Programa actualizado.');

        $program->refresh();

        $this->assertSame('Nombre Nuevo', $program->name);
        $this->assertSame('published', $program->status);
        $this->assertDatabaseHas('program_schedules', [
            'program_id' => $program->id,
            'weekday' => 5,
            'starts_at' => '18:00',
        ]);
        $this->assertDatabaseMissing('program_schedules', [
            'program_id' => $program->id,
            'weekday' => 1,
        ]);
    }

    public function test_a_program_can_be_published_and_deactivated_from_the_list(): void
    {
        $program = $this->createProgram('Programa de Prueba', [['weekday' => '2', 'starts_at' => '10:00', 'ends_at' => '11:00']]);

        $this->actingAs($this->admin, 'admin')
            ->patch('/admin/programacion/'.$program->id.'/estado', ['status' => 'published'])
            ->assertRedirect(route('admin.programs.index'));

        $this->assertSame('published', $program->refresh()->status);
        $this->get('/')->assertSee('Programa de Prueba');

        $this->actingAs($this->admin, 'admin')
            ->patch('/admin/programacion/'.$program->id.'/estado', ['status' => 'inactive'])
            ->assertRedirect(route('admin.programs.index'));

        $this->assertSame('inactive', $program->refresh()->status);
        $this->get('/')->assertDontSee('Programa de Prueba');
    }

    public function test_a_draft_program_is_not_public(): void
    {
        $this->createProgram('Programa Borrador', [['weekday' => '2', 'starts_at' => '10:00', 'ends_at' => '11:00']]);

        $this->get('/')->assertDontSee('Programa Borrador');
    }

    public function test_deleting_a_program_requires_the_confirmation_screen(): void
    {
        $program = $this->createProgram('Programa a Borrar', [['weekday' => '2', 'starts_at' => '10:00', 'ends_at' => '11:00']]);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/programacion/'.$program->id.'/eliminar')
            ->assertOk()
            ->assertSee('Programa a Borrar')
            ->assertSee('Eliminar definitivamente');

        $this->actingAs($this->admin, 'admin')
            ->delete('/admin/programacion/'.$program->id)
            ->assertRedirect(route('admin.programs.index'));

        $this->assertDatabaseMissing('programs', ['id' => $program->id]);
        $this->assertDatabaseMissing('program_schedules', ['program_id' => $program->id]);
        $this->assertSame('program.deleted', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_an_unknown_program_returns_not_found(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/programacion/999/editar')
            ->assertNotFound();
    }

    public function test_a_cover_can_be_uploaded(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->post('/admin/programacion', $this->payload([
                'cover' => ImageFixtures::png('portada.png'),
            ]))
            ->assertSessionHasNoErrors();

        $path = Program::query()->firstOrFail()->cover_path;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_a_non_image_cover_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->from('/admin/programacion/nuevo')
            ->post('/admin/programacion', $this->payload([
                'cover' => UploadedFile::fake()->create('archivo.pdf', 10, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('cover');

        $this->assertDatabaseCount('programs', 0);
    }

    /**
     * @param  array<int, array<string, string>>  $schedule
     */
    private function createProgram(string $name, array $schedule): Program
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/programacion', $this->payload([
                'name' => $name,
                'status' => 'draft',
                'schedule' => $schedule,
            ]))
            ->assertSessionHasNoErrors();

        return Program::query()->where('name', $name)->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Programa de Prueba',
            'description' => 'Datos de prueba.',
            'status' => 'draft',
            'sort_order' => 0,
            'schedule' => [['weekday' => '1', 'starts_at' => '08:00', 'ends_at' => '10:00']],
        ], $overrides);
    }
}
