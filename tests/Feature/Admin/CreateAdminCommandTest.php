<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_administrator_with_a_hashed_password(): void
    {
        $this->artisan('radio:create-admin', [
            '--name' => 'Root',
            '--email' => '  ROOT@Example.COM ',
        ])
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'strong-password')
            ->expectsQuestion('Repite la contraseña', 'strong-password')
            ->assertSuccessful();

        $admin = AdminUser::query()->where('email', 'root@example.com')->firstOrFail();

        $this->assertSame('Root', $admin->name);
        $this->assertTrue(Hash::check('strong-password', $admin->password));
        $this->assertNotSame('strong-password', $admin->password);
    }

    public function test_it_refuses_a_duplicate_email(): void
    {
        AdminUser::factory()->create(['email' => 'root@example.com']);

        $this->artisan('radio:create-admin', [
            '--name' => 'Root',
            '--email' => 'root@example.com',
        ])
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'strong-password')
            ->expectsQuestion('Repite la contraseña', 'strong-password')
            ->assertFailed();

        $this->assertDatabaseCount('admin_users', 1);
    }

    public function test_it_refuses_a_short_password(): void
    {
        $this->artisan('radio:create-admin', [
            '--name' => 'Root',
            '--email' => 'root@example.com',
        ])
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'short')
            ->expectsQuestion('Repite la contraseña', 'short')
            ->assertFailed();

        $this->assertDatabaseCount('admin_users', 0);
    }

    public function test_it_refuses_a_mismatched_confirmation(): void
    {
        $this->artisan('radio:create-admin', [
            '--name' => 'Root',
            '--email' => 'root@example.com',
        ])
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'strong-password')
            ->expectsQuestion('Repite la contraseña', 'other-password')
            ->assertFailed();

        $this->assertDatabaseCount('admin_users', 0);
    }

    public function test_it_refuses_to_run_without_interactive_input(): void
    {
        $this->withoutMockingConsoleOutput();

        $exitCode = $this->artisan('radio:create-admin', ['--no-interaction' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseCount('admin_users', 0);
    }
}
