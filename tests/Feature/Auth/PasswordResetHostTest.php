<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

final class PasswordResetHostTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_reset_link_uses_the_configured_app_url_instead_of_the_request_host(): void
    {
        Notification::fake();
        config(['app.url' => 'https://radio.example.test']);

        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->withHeader('Host', 'evil.example.com')
            ->post('/forgot-password', ['email' => 'ana@example.com'])
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $url = (string) $notification->toMail($user)->actionUrl;

            $this->assertStringStartsWith('https://radio.example.test/reset-password/', $url);
            $this->assertStringNotContainsString('evil.example.com', $url);

            return true;
        });
    }

    public function test_the_reset_link_uses_https_in_production(): void
    {
        Notification::fake();
        $this->app['env'] = 'production';
        config(['app.url' => 'https://radio.example.test']);
        $this->withoutMiddleware(PreventRequestForgery::class);

        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->withHeader('Host', 'evil.example.com')
            ->post('/forgot-password', ['email' => 'ana@example.com'])
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $url = (string) $notification->toMail($user)->actionUrl;

            $this->assertStringStartsWith('https://radio.example.test/reset-password/', $url);
            $this->assertStringNotContainsString('evil.example.com', $url);

            return true;
        });
    }

    public function test_local_environment_can_use_an_http_app_url(): void
    {
        Notification::fake();
        $this->app['env'] = 'local';
        config(['app.url' => 'http://localhost:8000']);
        $this->withoutMiddleware(PreventRequestForgery::class);

        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->post('/forgot-password', ['email' => 'ana@example.com'])
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $url = (string) $notification->toMail($user)->actionUrl;

            $this->assertStringStartsWith('http://localhost:8000/reset-password/', $url);

            return true;
        });
    }

    public function test_an_http_app_url_is_rejected_in_production(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'http://radio.example.test']);

        $this->withoutExceptionHandling();
        $this->expectException(RuntimeException::class);

        $this->get('/');
    }

    public function test_an_invalid_app_url_is_rejected(): void
    {
        config(['app.url' => 'not-a-url']);

        $this->withoutExceptionHandling();
        $this->expectException(RuntimeException::class);

        $this->get('/');
    }
}
