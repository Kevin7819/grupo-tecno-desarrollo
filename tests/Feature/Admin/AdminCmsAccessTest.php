<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminCmsAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, string>
     */
    private function protectedRoutes(): array
    {
        return [
            '/admin',
            '/admin/emisora',
            '/admin/programacion',
            '/admin/programacion/nuevo',
            '/admin/locutores',
            '/admin/locutores/nuevo',
        ];
    }

    public function test_guests_are_redirected_to_the_admin_login_from_every_cms_section(): void
    {
        foreach ($this->protectedRoutes() as $route) {
            $this->get($route)->assertRedirect('/admin/login');
        }
    }

    public function test_listeners_cannot_reach_any_cms_section(): void
    {
        $listener = User::factory()->create();

        foreach ($this->protectedRoutes() as $route) {
            $this->actingAs($listener)->get($route)->assertRedirect('/admin/login');
        }
    }

    public function test_an_administrator_sees_the_panel_navigation(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk()
            ->assertSee('Panel')
            ->assertSee('Emisora')
            ->assertSee('Programación')
            ->assertSee('Locutores')
            ->assertSee(route('admin.settings.edit'), false)
            ->assertSee(route('admin.programs.index'), false)
            ->assertSee(route('admin.hosts.index'), false);
    }

    public function test_the_cms_sections_render_for_an_administrator(): void
    {
        $admin = AdminUser::factory()->create();

        foreach ([
            '/admin/emisora',
            '/admin/programacion',
            '/admin/programacion/nuevo',
            '/admin/locutores',
            '/admin/locutores/nuevo',
        ] as $route) {
            $this->actingAs($admin, 'admin')->get($route)->assertOk();
        }
    }
}
