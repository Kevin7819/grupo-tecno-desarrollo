<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_is_available_to_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_a_listener_can_log_in_and_the_session_is_regenerated(): void
    {
        $user = User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        $this->get('/login');
        $previousSessionId = session()->getId();

        $response = $this->post('/login', [
            'email' => 'ana@example.com',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($previousSessionId, session()->getId());
    }

    public function test_invalid_credentials_are_rejected_with_a_generic_message(): void
    {
        User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'ana@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
        $this->assertGuest();
    }

    public function test_a_listener_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_authenticated_listeners_are_redirected_away_from_the_login_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect('/');
    }

    public function test_login_is_rate_limited(): void
    {
        User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        $this->from('/login');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'email' => 'ana@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login');
        }

        $this->post('/login', [
            'email' => 'ana@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_login_validation_rejects_malformed_input(): void
    {
        $this->from('/login')
            ->post('/login', ['email' => 'not-an-email', 'password' => ''])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }
}
