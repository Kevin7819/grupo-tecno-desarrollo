<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_web_group_keeps_csrf_protection(): void
    {
        $kernel = $this->app->make(Kernel::class);

        $this->assertTrue(
            collect($kernel->getMiddlewareGroups()['web'])->contains(
                fn (string $middleware): bool => is_a($middleware, PreventRequestForgery::class, true),
            ),
            'The web middleware group must keep CSRF protection.',
        );
    }

    public function test_content_security_policy_is_sent_outside_local_environments(): void
    {
        $this->app['env'] = 'production';

        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');

        $policy = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("script-src 'self'", $policy);
        $this->assertStringContainsString('media-src https:', $policy);
    }

    public function test_admin_routes_run_inside_the_web_group(): void
    {
        foreach (['admin.login.store', 'admin.logout'] as $name) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is missing.");
            $this->assertContains('web', $route->gatherMiddleware());
        }
    }

    public function test_authentication_routes_run_inside_the_web_group(): void
    {
        foreach (['login.store', 'register.store', 'password.email', 'password.store', 'profile.update'] as $name) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is missing.");
            $this->assertContains('web', $route->gatherMiddleware());
        }
    }

    public function test_sensitive_routes_declare_rate_limiting(): void
    {
        $expected = [
            'login.store' => 'throttle:login',
            'admin.login.store' => 'throttle:admin-login',
            'register.store' => 'throttle:register',
            'password.email' => 'throttle:password-email',
            'password.store' => 'throttle:password-reset',
        ];

        foreach ($expected as $name => $middleware) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is missing.");
            $this->assertContains($middleware, $route->gatherMiddleware());
        }
    }

    public function test_admin_routes_use_the_admin_auth_middleware(): void
    {
        foreach (['admin.dashboard', 'admin.logout'] as $name) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is missing.");
            $this->assertContains('admin.auth', $route->gatherMiddleware());
        }
    }
}
