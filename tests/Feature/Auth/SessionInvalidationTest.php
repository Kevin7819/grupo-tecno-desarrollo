<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

final class SessionInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_changing_the_password_keeps_the_current_session_and_invalidates_the_others(): void
    {
        $ana = User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('current-password'),
        ]);

        $otherSession = $this->loginAndCaptureSession('ana@example.com', 'current-password');

        $this->put('/profile/password', [
            'current_password' => 'current-password',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('brand-new-password', $ana->refresh()->password));

        Auth::forgetGuards();

        $this->get('/profile')
            ->assertOk()
            ->assertSee('ana@example.com');

        $this->restoreSession($otherSession);

        $this->get('/profile')->assertRedirect('/login');
        $this->assertGuest('web');
    }

    public function test_resetting_the_password_invalidates_existing_sessions(): void
    {
        $ana = User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('current-password'),
        ]);

        $oldSession = $this->loginAndCaptureSession('ana@example.com', 'current-password');

        Auth::forgetGuards();

        $this->get('/profile')->assertOk();

        $this->flushSession();
        Auth::forgetGuards();

        $token = Password::broker()->createToken($ana);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'ana@example.com',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('brand-new-password', $ana->refresh()->password));

        $this->restoreSession($oldSession);

        $this->get('/profile')->assertRedirect('/login');
        $this->assertGuest('web');
    }

    /**
     * @return array<string, mixed>
     */
    private function loginAndCaptureSession(string $email, string $password): array
    {
        $this->post('/login', [
            'email' => $email,
            'password' => $password,
        ])->assertRedirect();

        $session = $this->app['session']->all();

        $this->assertArrayHasKey('password_hash_web', $session);

        return $session;
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function restoreSession(array $session): void
    {
        $this->flushSession();
        $this->withSession($session);
        Auth::forgetGuards();
    }
}
