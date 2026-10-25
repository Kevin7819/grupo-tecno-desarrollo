<?php

namespace Tests\Support;

use App\Application\Account\PasswordHasher;

final class FakePasswordHasher implements PasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return 'hashed:'.$plainPassword;
    }

    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return $passwordHash === $this->hash($plainPassword);
    }
}
