<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create([
            'email' => 'admin@radio.test',
            'password' => bcrypt('admin-password'),
        ]);
    }

    public function test_an_administrator_can_create_another_account(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/cuentas', [
                'name' => 'Segunda Persona',
                'email' => 'segunda@radio.test',
                'password' => 'clave-segura-1',
                'password_confirmation' => 'clave-segura-1',
            ])
            ->assertRedirect(route('admin.accounts.index'))
            ->assertSessionHas('status', 'Cuenta administrativa creada.');

        $account = AdminUser::query()->where('email', 'segunda@radio.test')->firstOrFail();

        $this->assertSame('Segunda Persona', $account->name);
        $this->assertTrue(Hash::check('clave-segura-1', $account->password));
        $this->assertSame('administrator.created', AdminActivity::query()->latest('id')->firstOrFail()->action);

        $this->post('/admin/logout')->assertRedirect('/admin/login');

        $this->post('/admin/login', [
            'email' => 'segunda@radio.test',
            'password' => 'clave-segura-1',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($account, 'admin');
    }

    public function test_the_account_form_rejects_a_duplicate_email(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/cuentas', [
                'name' => 'Otra Persona',
                'email' => 'admin@radio.test',
                'password' => 'clave-segura-1',
                'password_confirmation' => 'clave-segura-1',
            ])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('admin_users', 1);
    }

    public function test_the_account_form_rejects_a_short_password(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/admin/cuentas', [
                'name' => 'Otra Persona',
                'email' => 'otra@radio.test',
                'password' => 'corta',
                'password_confirmation' => 'corta',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('admin_users', 1);
    }

    public function test_an_administrator_can_update_an_account_and_its_password(): void
    {
        $account = AdminUser::factory()->create([
            'name' => 'Nombre Anterior',
            'email' => 'anterior@radio.test',
            'password' => bcrypt('clave-anterior-1'),
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/cuentas/'.$account->id, [
                'name' => 'Nombre Nuevo',
                'email' => 'nuevo@radio.test',
                'password' => 'clave-nueva-1',
                'password_confirmation' => 'clave-nueva-1',
            ])
            ->assertRedirect(route('admin.accounts.index'))
            ->assertSessionHas('status', 'Cuenta administrativa actualizada.');

        $account->refresh();

        $this->assertSame('Nombre Nuevo', $account->name);
        $this->assertSame('nuevo@radio.test', $account->email);
        $this->assertTrue(Hash::check('clave-nueva-1', $account->password));
        $this->assertSame('administrator.updated', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_an_administrator_can_update_an_account_without_changing_its_password(): void
    {
        $account = AdminUser::factory()->create([
            'email' => 'anterior@radio.test',
            'password' => bcrypt('clave-anterior-1'),
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put('/admin/cuentas/'.$account->id, [
                'name' => 'Nombre Nuevo',
                'email' => 'nuevo@radio.test',
            ])
            ->assertRedirect();

        $account->refresh();

        $this->assertTrue(Hash::check('clave-anterior-1', $account->password));
    }

    public function test_an_administrator_cannot_delete_their_own_account(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->delete('/admin/cuentas/'.$this->admin->id)
            ->assertRedirect(route('admin.accounts.index'))
            ->assertSessionHasErrors('account');

        $this->assertDatabaseHas('admin_users', ['id' => $this->admin->id]);
    }

    public function test_an_administrator_can_delete_another_account(): void
    {
        $account = AdminUser::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/cuentas/'.$account->id.'/eliminar')
            ->assertOk()
            ->assertSee($account->email);

        $this->actingAs($this->admin, 'admin')
            ->delete('/admin/cuentas/'.$account->id)
            ->assertRedirect(route('admin.accounts.index'))
            ->assertSessionHas('status', 'Cuenta administrativa eliminada.');

        $this->assertDatabaseMissing('admin_users', ['id' => $account->id]);
        $this->assertSame('administrator.deleted', AdminActivity::query()->latest('id')->firstOrFail()->action);
    }

    public function test_changing_their_own_password_keeps_the_administrator_signed_in(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/cuentas/'.$this->admin->id, [
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'password' => 'clave-propia-1',
                'password_confirmation' => 'clave-propia-1',
            ])
            ->assertRedirect();

        $this->assertAuthenticatedAs($this->admin, 'admin');
        $this->assertTrue(Hash::check('clave-propia-1', $this->admin->refresh()->password));
    }
}
