<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Account\ListenerAccount;
use App\Application\Account\ListenerRepository;
use App\Application\Account\NewListener;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class EloquentListenerRepository implements ListenerRepository
{
    public function existsByEmail(string $email): bool
    {
        return User::query()->where('email', $email)->exists();
    }

    public function emailTakenByAnother(int $listenerId, string $email): bool
    {
        return User::query()
            ->where('email', $email)
            ->whereKeyNot($listenerId)
            ->exists();
    }

    public function create(NewListener $listener): ListenerAccount
    {
        $user = User::query()->create([
            'name' => $listener->name,
            'email' => $listener->email,
            'password' => $listener->passwordHash,
        ]);

        return $this->toAccount($user);
    }

    public function updateProfile(int $listenerId, string $name, string $email): ListenerAccount
    {
        $user = User::query()->findOrFail($listenerId);
        $user->forceFill([
            'name' => $name,
            'email' => $email,
        ])->save();

        return $this->toAccount($user);
    }

    public function passwordHashFor(int $listenerId): ?string
    {
        $hash = User::query()->whereKey($listenerId)->value('password');

        return is_string($hash) ? $hash : null;
    }

    public function updatePassword(int $listenerId, string $passwordHash): void
    {
        // The value is already hashed by the application layer. Writing through
        // the query builder avoids the model's "hashed" cast re-hashing it.
        DB::table('users')->where('id', $listenerId)->update([
            'password' => $passwordHash,
            'remember_token' => null,
            'updated_at' => now(),
        ]);
    }

    private function toAccount(User $user): ListenerAccount
    {
        return new ListenerAccount(
            id: (int) $user->getKey(),
            name: (string) $user->name,
            email: (string) $user->email,
        );
    }
}
