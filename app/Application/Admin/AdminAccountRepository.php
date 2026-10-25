<?php

namespace App\Application\Admin;

interface AdminAccountRepository
{
    public function existsByEmail(string $email): bool;

    public function create(string $name, string $email, string $passwordHash): AdminAccount;
}
