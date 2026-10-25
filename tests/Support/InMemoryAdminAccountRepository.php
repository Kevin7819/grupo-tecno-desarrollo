<?php

namespace Tests\Support;

use App\Application\Admin\AdminAccount;
use App\Application\Admin\AdminAccountRepository;

final class InMemoryAdminAccountRepository implements AdminAccountRepository
{
    /**
     * @var array<int, array{name: string, email: string, password: string}>
     */
    private array $admins = [];

    private int $nextId = 1;

    public function existsByEmail(string $email): bool
    {
        foreach ($this->admins as $admin) {
            if ($admin['email'] === $email) {
                return true;
            }
        }

        return false;
    }

    public function create(string $name, string $email, string $passwordHash): AdminAccount
    {
        $id = $this->nextId++;

        $this->admins[$id] = [
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ];

        return new AdminAccount($id, $name, $email);
    }

    public function passwordHashFor(string $email): ?string
    {
        foreach ($this->admins as $admin) {
            if ($admin['email'] === $email) {
                return $admin['password'];
            }
        }

        return null;
    }
}
