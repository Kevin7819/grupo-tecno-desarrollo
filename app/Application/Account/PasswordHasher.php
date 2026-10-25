<?php

namespace App\Application\Account;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;

    public function verify(string $plainPassword, string $passwordHash): bool;
}
