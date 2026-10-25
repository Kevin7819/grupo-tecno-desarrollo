<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_profile(): void
    {
        $this->get('/profile')->assertRedirect('/login');
        $this->patch('/profile', [])->assertRedirect('/login');
        $this->put('/profile/password', [])->assertRedirect('/login');
    }

    public function test_a_listener_sees_only_their_own_account_data(): void
    {
        $ana = User::factory()->create([
            'name' => 'Ana',
            'email' => 'ana@example.com',
        ]);

        User::factory()->create([
            'name' => 'Beto',
            'email' => 'beto@example.com',
        ]);

        $this->actingAs($ana)
            ->get('/profile')
            ->assertOk()
            ->assertSee('ana@example.com')
            ->assertDontSee('beto@example.com');
    }

    public function test_a_listener_can_update_their_profile(): void
    {
        $ana = User::factory()->create([
            'name' => 'Ana',
            'email' => 'ana@example.com',
        ]);

        $this->actingAs($ana)
            ->patch('/profile', [
                'name' => 'Ana María',
                'email' => 'ana.maria@example.com',
            ])
            ->assertRedirect();

        $ana->refresh();

        $this->assertSame('Ana María', $ana->name);
        $this->assertSame('ana.maria@example.com', $ana->email);
    }

    public function test_a_listener_cannot_take_another_listeners_email(): void
    {
        $ana = User::factory()->create(['email' => 'ana@example.com']);
        $beto = User::factory()->create(['email' => 'beto@example.com']);

        $this->actingAs($ana)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Ana',
                'email' => 'beto@example.com',
            ])
            ->assertSessionHasErrors('email');

        $this->assertSame('ana@example.com', $ana->refresh()->email);
        $this->assertSame('beto@example.com', $beto->refresh()->email);
    }

    public function test_the_current_password_must_match_to_change_it(): void
    {
        $ana = User::factory()->create(['password' => bcrypt('current-password')]);

        $this->actingAs($ana)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('current-password', $ana->refresh()->password));
    }

    public function test_a_listener_can_change_their_password(): void
    {
        $ana = User::factory()->create([
            'email' => 'ana@example.com',
            'password' => bcrypt('current-password'),
        ]);

        $this->actingAs($ana)
            ->put('/profile/password', [
                'current_password' => 'current-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertRedirect();

        $ana->refresh();

        $this->assertTrue(Hash::check('brand-new-password', $ana->password));
        $this->assertFalse(Hash::check('current-password', $ana->password));
    }

    public function test_password_changes_are_rate_limited_per_listener_even_across_ips(): void
    {
        $ana = User::factory()->create(['password' => bcrypt('current-password')]);

        $this->actingAs($ana)->from('/profile');
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])->assertSessionHasErrors('current_password');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2']);

        $this->put('/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertStatus(429);
    }

    public function test_a_listener_can_change_the_password_within_the_limit(): void
    {
        $ana = User::factory()->create(['password' => bcrypt('current-password')]);

        $this->actingAs($ana)->from('/profile');

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])->assertSessionHasErrors('current_password');
        }

        $this->put('/profile/password', [
            'current_password' => 'current-password',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('brand-new-password', $ana->refresh()->password));
    }

    public function test_the_password_limit_is_scoped_to_the_authenticated_listener(): void
    {
        $ana = User::factory()->create(['password' => bcrypt('current-password')]);
        $beto = User::factory()->create(['password' => bcrypt('current-password')]);

        $this->actingAs($ana)->from('/profile');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])->assertSessionHasErrors('current_password');
        }

        $this->flushSession();

        $this->actingAs($beto)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertSessionHasErrors('current_password');
    }
}
