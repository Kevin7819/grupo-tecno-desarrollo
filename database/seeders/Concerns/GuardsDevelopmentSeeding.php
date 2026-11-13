<?php

namespace Database\Seeders\Concerns;

/**
 * Environment gate shared by the development seeders.
 *
 * Seeders that create authenticatable accounts must never run because
 * somebody typed `db:seed` on a server. They run only in the environments
 * listed in `config/seeding.php` and only with explicitly configured
 * credentials, so the safe behaviour is also the default one.
 */
trait GuardsDevelopmentSeeding
{
    private const MIN_PASSWORD_LENGTH = 12;

    private function seedingAllowed(): bool
    {
        $allowed = (array) config('seeding.allowed_environments', ['local']);

        return $allowed !== [] && app()->environment($allowed);
    }

    private function passwordIsStrongEnough(string $password): bool
    {
        return mb_strlen($password) >= self::MIN_PASSWORD_LENGTH;
    }
}
