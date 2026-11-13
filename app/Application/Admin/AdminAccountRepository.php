<?php

namespace App\Application\Admin;

interface AdminAccountRepository
{
    public function existsByEmail(string $email): bool;

    public function existsByEmailExcept(string $email, int $exceptId): bool;

    public function create(string $name, string $email, string $passwordHash): AdminAccount;

    /**
     * @return AdminAccount[]
     */
    public function all(): array;

    public function find(int $id): ?AdminAccount;

    public function update(AdminAccount $account, string $name, string $email): AdminAccount;

    public function updatePassword(int $id, string $passwordHash): void;

    public function delete(AdminAccount $account): void;

    public function count(): int;
}
