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

    public function existsByEmailExcept(string $email, int $exceptId): bool
    {
        return AdminUser::query()
            ->where('email', $email)
            ->whereKeyNot($exceptId)
            ->exists();
    }

    public function create(string $name, string $email, string $passwordHash): AdminAccount
    {
        return $this->toDomain(AdminUser::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ]));
    }

    public function all(): array
    {
        return AdminUser::query()
            ->orderBy('name')
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function find(int $id): ?AdminAccount
    {
        $model = AdminUser::query()->find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function update(AdminAccount $account, string $name, string $email): AdminAccount
    {
        $model = AdminUser::query()->findOrFail($account->id);
        $model->update(['name' => $name, 'email' => $email]);

        return $this->toDomain($model);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        AdminUser::query()->findOrFail($id)->update(['password' => $passwordHash]);
    }

    public function delete(AdminAccount $account): void
    {
        AdminUser::query()->findOrFail($account->id)->delete();
    }

    public function count(): int
    {
        return AdminUser::query()->count();
    }

    private function toDomain(AdminUser $model): AdminAccount
    {
        return new AdminAccount(
            id: (int) $model->getKey(),
            name: (string) $model->name,
            email: (string) $model->email,
            createdAt: $model->created_at !== null
                ? \DateTimeImmutable::createFromInterface($model->created_at)
                : null,
        );
    }
}
