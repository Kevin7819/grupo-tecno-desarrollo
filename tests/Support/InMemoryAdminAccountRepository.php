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

    public function existsByEmailExcept(string $email, int $exceptId): bool
    {
        foreach ($this->admins as $id => $admin) {
            if ($id !== $exceptId && $admin['email'] === $email) {
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

    /**
     * @return AdminAccount[]
     */
    public function all(): array
    {
        $accounts = [];

        foreach ($this->admins as $id => $admin) {
            $accounts[] = new AdminAccount($id, $admin['name'], $admin['email']);
        }

        return $accounts;
    }

    public function find(int $id): ?AdminAccount
    {
        if (! isset($this->admins[$id])) {
            return null;
        }

        return new AdminAccount($id, $this->admins[$id]['name'], $this->admins[$id]['email']);
    }

    public function update(AdminAccount $account, string $name, string $email): AdminAccount
    {
        $this->admins[$account->id]['name'] = $name;
        $this->admins[$account->id]['email'] = $email;

        return new AdminAccount($account->id, $name, $email);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $this->admins[$id]['password'] = $passwordHash;
    }

    public function delete(AdminAccount $account): void
    {
        unset($this->admins[$account->id]);
    }

    public function count(): int
    {
        return count($this->admins);
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
