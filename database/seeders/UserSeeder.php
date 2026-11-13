<?php

namespace Database\Seeders;

use App\Application\Account\PasswordHasher;
use App\Models\User;
use Database\Seeders\Concerns\GuardsDevelopmentSeeding;
use Illuminate\Database\Seeder;

/**
 * Creates a few listener accounts so the public site and the panel can be
 * exercised with both kinds of account.
 *
 * Listeners use a different guard (`web` / `users`) than administrators, so
 * these accounts must never reach `/admin`. Their addresses are reserved
 * example.com addresses and their password comes from SEED_USER_PASSWORD;
 * when it is absent the seeder does nothing.
 */
final class UserSeeder extends Seeder
{
    use GuardsDevelopmentSeeding;

    /**
     * @var list<array{name: string, email: string}>
     */
    private const LISTENERS = [
        ['name' => 'Oyente de prueba 1', 'email' => 'oyente1@example.com'],
        ['name' => 'Oyente de prueba 2', 'email' => 'oyente2@example.com'],
        ['name' => 'Oyente de prueba 3', 'email' => 'oyente3@example.com'],
    ];

    public function __construct(private readonly PasswordHasher $hasher) {}

    public function run(): void
    {
        if (! $this->seedingAllowed()) {
            $this->command?->warn(
                'UserSeeder omitido: el entorno ['.app()->environment().'] no está autorizado en config/seeding.php.'
            );

            return;
        }

        $password = (string) config('seeding.listeners.password');

        if ($password === '') {
            $this->command?->warn('UserSeeder omitido: definí SEED_USER_PASSWORD en .env.');

            return;
        }

        if (! $this->passwordIsStrongEnough($password)) {
            $this->command?->error(
                'UserSeeder omitido: SEED_USER_PASSWORD debe tener al menos '.self::MIN_PASSWORD_LENGTH.' caracteres.'
            );

            return;
        }

        $hash = $this->hasher->hash($password);
        $created = 0;

        foreach (self::LISTENERS as $listener) {
            $exists = User::query()->where('email', $listener['email'])->exists();

            if ($exists) {
                continue;
            }

            User::factory()->create([
                'name' => $listener['name'],
                'email' => $listener['email'],
                'password' => $hash,
                'email_verified_at' => now(),
            ]);

            $created++;
        }

        $this->command?->info(
            $created === 0
                ? 'Los oyentes de prueba ya existían; no se modificó ninguno.'
                : "Oyentes de prueba creados: {$created}."
        );
    }
}
