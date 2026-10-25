<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Admin\AdminAccount;
use App\Application\Admin\AdminAccountRepository;
use App\Models\AdminUser;

final class EloquentAdminAccountRepository implements AdminAccountRepository
{
    public function existsByEmail(string $email): bool
    {
        return AdminUser::query()->where('email', $email)->exists();
    }

    public function create(string $name, string $email, string $passwordHash): AdminAccount
    {
        $admin = AdminUser::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ]);

        return new AdminAccount(
            id: (int) $admin->getKey(),
            name: (string) $admin->name,
            email: (string) $admin->email,
        );
    }
}
