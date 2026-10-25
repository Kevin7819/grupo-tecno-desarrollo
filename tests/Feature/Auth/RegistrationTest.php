<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_is_available_to_guests(): void
    {
        $this->get('/register')->assertOk()->assertSee('Crear cuenta');
    }

    public function test_a_listener_can_register_and_is_logged_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'ana@example.com')->firstOrFail();

        $this->assertSame('Ana Pérez', $user->name);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertNotSame('secret-password', $user->password);
    }

    public function test_the_email_is_normalized_before_storage(): void
    {
        $this->post('/register', [
            'name' => 'Ana',
            'email' => '  ANA@Example.COM ',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_registration_validates_every_field_on_the_server(): void
    {
        $this->from('/register')
            ->post('/register', [])
            ->assertRedirect('/register')
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->from('/register')
            ->post('/register', [
                'name' => 'A',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_a_duplicate_email_is_rejected_case_insensitively(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $this->from('/register')
            ->post('/register', [
                'name' => 'Otra Ana',
                'email' => 'ANA@EXAMPLE.COM',
                'password' => 'secret-password',
                'password_confirmation' => 'secret-password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_is_rate_limited(): void
    {
        $this->from('/register');

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post('/register', [])->assertRedirect('/register');
        }

        $this->post('/register', [])->assertStatus(429);
    }
}
