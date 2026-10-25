<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/admin/logout')->assertRedirect('/admin/login');
    }

    public function test_listeners_cannot_reach_the_admin_panel(): void
    {
        $listener = User::factory()->create();

        $this->actingAs($listener)
            ->get('/admin')
            ->assertRedirect('/admin/login');

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_listener_credentials_do_not_authenticate_the_admin_guard(): void
    {
        User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('listener-password'),
        ]);

        $this->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'ana@example.com',
                'password' => 'listener-password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_an_administrator_can_log_in_without_the_listener_guard(): void
    {
        $admin = AdminUser::factory()->create([
            'email' => 'root@example.com',
            'password' => bcrypt('admin-password'),
        ]);

        $this->post('/admin/login', [
            'email' => 'root@example.com',
            'password' => 'admin-password',
        ])->assertRedirect('/admin');

        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertFalse(Auth::guard('web')->check());
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertGuest('web');
    }

    public function test_an_administrator_can_reach_the_panel(): void
    {
        $admin = AdminUser::factory()->create(['name' => 'Root']);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk()
            ->assertSee('Panel administrativo')
            ->assertSee('Root');
    }

    public function test_an_administrator_can_log_out(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest('admin');
    }

    public function test_an_authenticated_administrator_is_redirected_away_from_the_login_form(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/login')
            ->assertRedirect('/admin');
    }

    public function test_the_admin_guard_is_not_valid_for_listener_routes(): void
    {
        AdminUser::factory()->create([
            'email' => 'root@example.com',
            'password' => bcrypt('admin-password'),
        ]);

        $this->post('/admin/login', [
            'email' => 'root@example.com',
            'password' => 'admin-password',
        ])->assertRedirect('/admin');

        $this->assertTrue(Auth::guard('admin')->check());

        $this->get('/profile')->assertRedirect('/login');

        $this->assertGuest('web');
    }

    public function test_there_is_no_public_administrator_registration(): void
    {
        $this->assertFalse(Route::has('admin.register'));
        $this->assertFalse(Route::has('admin.register.store'));

        $this->get('/admin/register')->assertNotFound();
    }

    public function test_admin_login_is_rate_limited(): void
    {
        AdminUser::factory()->create([
            'email' => 'root@example.com',
            'password' => bcrypt('admin-password'),
        ]);

        $this->from('/admin/login');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', [
                'email' => 'root@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/admin/login');
        }

        $this->post('/admin/login', [
            'email' => 'root@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);

        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_the_admin_login_form_does_not_offer_remember_me(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee('Recordarme');
    }

    public function test_admin_login_ignores_the_remember_flag(): void
    {
        $admin = AdminUser::factory()->create([
            'email' => 'root@example.com',
            'password' => bcrypt('admin-password'),
            'remember_token' => null,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'root@example.com',
            'password' => 'admin-password',
            'remember' => '1',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNull($admin->refresh()->remember_token);
        $response->assertCookieMissing(Auth::guard('admin')->getRecallerName());
    }
}
