<?php

namespace Database\Seeders;

use App\Application\Account\PasswordHasher;
use App\Models\AdminUser;
use Database\Seeders\Concerns\GuardsDevelopmentSeeding;
use Illuminate\Database\Seeder;

/**
 * Creates the development administrator used to inspect the panel.
 *
 * The account is ordinary from the application's point of view: the admin
 * panel is guarded by the `admin` guard over the `admin_users` provider, so
 * being a row in that table IS the administrative grant. There are no role
 * or permission tables in this project, and none are needed.
 *
 * Safety rules:
 * - never runs outside `config('seeding.allowed_environments')`;
 * - never invents a password: a missing or weak one aborts the seed;
 * - never modifies an existing account with the same e-mail.
 */
final class AdminUserSeeder extends Seeder
{
    use GuardsDevelopmentSeeding;

    public function __construct(private readonly PasswordHasher $hasher) {}

    public function run(): void
    {
        if (! $this->seedingAllowed()) {
            $this->command?->warn(
                'AdminUserSeeder omitido: el entorno ['.app()->environment().'] no está autorizado en config/seeding.php.'
            );

            return;
        }

        $name = trim((string) config('seeding.admin.name'));
        $email = mb_strtolower(trim((string) config('seeding.admin.email')));
        $password = (string) config('seeding.admin.password');

        if ($email === '' || $password === '') {
            $this->command?->warn(
                'AdminUserSeeder omitido: definí ADMIN_SEED_EMAIL y ADMIN_SEED_PASSWORD en .env.'
            );

            return;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->command?->error('AdminUserSeeder omitido: ADMIN_SEED_EMAIL no es un correo válido.');

            return;
        }

        if (! $this->passwordIsStrongEnough($password)) {
            $this->command?->error(
                'AdminUserSeeder omitido: ADMIN_SEED_PASSWORD debe tener al menos '.self::MIN_PASSWORD_LENGTH.' caracteres.'
            );

            return;
        }

        if (AdminUser::query()->where('email', $email)->exists()) {
            $this->command?->info("El administrador {$email} ya existe; no se modificó.");

            return;
        }

        AdminUser::query()->create([
            'name' => $name !== '' ? $name : 'Administrador de desarrollo',
            'email' => $email,
            'password' => $this->hasher->hash($password),
        ]);

        $this->command?->info("Administrador de desarrollo creado: {$email}");
    }
}
