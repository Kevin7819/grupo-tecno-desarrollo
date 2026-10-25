<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_request_form_is_available_to_guests(): void
    {
        $this->get('/forgot-password')->assertOk()->assertSee('Recuperar contraseña');
    }

    public function test_a_reset_link_is_sent_to_a_registered_listener(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->post('/forgot-password', ['email' => 'ana@example.com'])
            ->assertSessionHas(
                'status',
                'Si el correo está registrado, recibirás un enlace para restablecer la contraseña.',
            );

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_the_response_is_identical_for_an_unknown_email(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nobody@example.com'])
            ->assertSessionHas(
                'status',
                'Si el correo está registrado, recibirás un enlace para restablecer la contraseña.',
            );

        Notification::assertNothingSent();
    }

    public function test_the_request_is_rate_limited(): void
    {
        Notification::fake();

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post('/forgot-password', ['email' => 'nobody@example.com'])->assertRedirect();
        }

        $this->post('/forgot-password', ['email' => 'nobody@example.com'])->assertStatus(429);
    }

    public function test_a_password_can_be_reset_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'ana@example.com']);
        $token = $this->requestResetToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'ana@example.com',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertRedirect('/login');

        $user->refresh();

        $this->assertTrue(Hash::check('brand-new-password', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
    }

    public function test_a_token_cannot_be_reused(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'ana@example.com']);
        $token = $this->requestResetToken($user);

        $payload = [
            'token' => $token,
            'email' => 'ana@example.com',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ];

        $this->post('/reset-password', $payload)->assertRedirect('/login');

        $this->from('/reset-password/'.$token)
            ->post('/reset-password', $payload)
            ->assertSessionHasErrors('email');

        $user->refresh();

        $this->assertTrue(Hash::check('brand-new-password', $user->password));
    }

    public function test_an_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->from('/reset-password/invalid-token')
            ->post('/reset-password', [
                'token' => 'invalid-token',
                'email' => 'ana@example.com',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertSessionHasErrors('email');

        $user->refresh();

        $this->assertFalse(Hash::check('brand-new-password', $user->password));
    }

    public function test_a_weak_password_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'ana@example.com']);
        $token = $this->requestResetToken($user);

        $this->from('/reset-password/'.$token)
            ->post('/reset-password', [
                'token' => $token,
                'email' => 'ana@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertSessionHasErrors('password');
    }

    private function requestResetToken(User $user): string
    {
        $this->post('/forgot-password', ['email' => $user->email]);

        $token = null;

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function (ResetPassword $notification) use (&$token): bool {
                $token = $notification->token;

                return true;
            },
        );

        $this->assertIsString($token);

        return $token;
    }
}
