<?php

namespace Tests\Feature\Security;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_default_seeder_creates_no_authenticatable_accounts(): void
    {
        $this->seed();

        $this->assertSame(0, User::query()->count());
        $this->assertSame(0, AdminUser::query()->count());

        $this->assertFalse(Auth::guard('web')->attempt([
            'email' => 'test@example.com',
            'password' => 'password',
        ]));

        $this->assertFalse(Auth::guard('admin')->attempt([
            'email' => 'test@example.com',
            'password' => 'password',
        ]));
    }
}
