<?php

namespace Tests\Feature\Security;

use App\Models\AdminUser;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class DevelopmentSeedersTest extends TestCase
{
    use RefreshDatabase;

    private const ADMIN_EMAIL = 'admin.dev@example.com';

    private const ADMIN_PASSWORD = 'dev-admin-password-1234';

    private const LISTENER_PASSWORD = 'dev-listener-password-1234';

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function allowSeeding(array $overrides = []): void
    {
        config(array_merge([
            'seeding.allowed_environments' => ['testing'],
            'seeding.admin.name' => 'Administrador de desarrollo',
            'seeding.admin.email' => self::ADMIN_EMAIL,
            'seeding.admin.password' => self::ADMIN_PASSWORD,
            'seeding.listeners.password' => self::LISTENER_PASSWORD,
        ], $overrides));
    }

    public function test_seeders_do_nothing_in_an_environment_that_is_not_allowed(): void
    {
        // The default allow-list is ['local']; the suite runs as 'testing'.
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, User::query()->count());
        $this->assertSame(0, AdminUser::query()->count());

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, AdminUser::query()->count());
    }

    public function test_the_admin_seeder_creates_the_development_administrator(): void
    {
        $this->allowSeeding();

        $this->seed(AdminUserSeeder::class);

        $admin = AdminUser::query()->sole();

        $this->assertSame(self::ADMIN_EMAIL, $admin->email);
        $this->assertSame('Administrador de desarrollo', $admin->name);
        $this->assertNotSame(self::ADMIN_PASSWORD, $admin->password);
        $this->assertTrue(Hash::check(self::ADMIN_PASSWORD, $admin->password));
        $this->assertTrue(
            Auth::guard('admin')->attempt([
                'email' => self::ADMIN_EMAIL,
                'password' => self::ADMIN_PASSWORD,
            ])
        );
    }

    public function test_the_admin_seeder_is_idempotent(): void
    {
        $this->allowSeeding();

        $this->seed(AdminUserSeeder::class);
        $firstHash = AdminUser::query()->sole()->password;

        $this->seed(AdminUserSeeder::class);

        $admin = AdminUser::query()->sole();

        $this->assertSame($firstHash, $admin->password);
        $this->assertTrue(Hash::check(self::ADMIN_PASSWORD, $admin->password));
    }

    public function test_the_admin_seeder_does_not_modify_an_existing_account(): void
    {
        $existing = AdminUser::factory()->create([
            'email' => self::ADMIN_EMAIL,
            'password' => bcrypt('an-existing-real-password'),
        ]);

        $this->allowSeeding();

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, AdminUser::query()->count());
        $this->assertTrue(Hash::check('an-existing-real-password', $existing->refresh()->password));
        $this->assertFalse(Hash::check(self::ADMIN_PASSWORD, $existing->password));
    }

    public function test_the_admin_seeder_skips_without_configured_credentials(): void
    {
        $this->allowSeeding([
            'seeding.admin.email' => null,
            'seeding.admin.password' => null,
        ]);

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, AdminUser::query()->count());
    }

    public function test_the_admin_seeder_rejects_a_weak_password(): void
    {
        $this->allowSeeding(['seeding.admin.password' => 'short']);

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, AdminUser::query()->count());
    }

    public function test_the_admin_seeder_rejects_an_invalid_email(): void
    {
        $this->allowSeeding(['seeding.admin.email' => 'not-an-email']);

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, AdminUser::query()->count());
    }

    public function test_the_user_seeder_creates_listeners_with_hashed_passwords(): void
    {
        $this->allowSeeding();

        $this->seed(UserSeeder::class);

        $listeners = User::query()->orderBy('email')->get();

        $this->assertCount(3, $listeners);
        $this->assertSame(
            ['oyente1@example.com', 'oyente2@example.com', 'oyente3@example.com'],
            $listeners->pluck('email')->all()
        );

        foreach ($listeners as $listener) {
            $this->assertNotSame(self::LISTENER_PASSWORD, $listener->password);
            $this->assertTrue(Hash::check(self::LISTENER_PASSWORD, $listener->password));
        }

        $this->assertTrue(
            Auth::guard('web')->attempt([
                'email' => 'oyente1@example.com',
                'password' => self::LISTENER_PASSWORD,
            ])
        );

        $this->assertFalse(
            Auth::guard('admin')->attempt([
                'email' => 'oyente1@example.com',
                'password' => self::LISTENER_PASSWORD,
            ])
        );
    }

    public function test_the_user_seeder_is_idempotent(): void
    {
        $this->allowSeeding();

        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(3, User::query()->count());
    }

    public function test_the_user_seeder_skips_without_a_configured_password(): void
    {
        $this->allowSeeding(['seeding.listeners.password' => null]);

        $this->seed(UserSeeder::class);

        $this->assertSame(0, User::query()->count());
    }

    public function test_the_default_seeder_runs_both_seeders_when_allowed(): void
    {
        $this->allowSeeding();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, User::query()->count());
        $this->assertSame(1, AdminUser::query()->count());

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, User::query()->count());
        $this->assertSame(1, AdminUser::query()->count());
    }

    public function test_the_seeded_administrator_can_reach_the_panel_through_http(): void
    {
        $this->allowSeeding();
        $this->seed(AdminUserSeeder::class);

        $this->post('/admin/login', [
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
        ])->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Panel administrativo');

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }
}
