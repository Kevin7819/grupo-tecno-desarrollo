<?php

namespace Tests\Support;

use App\Application\Account\ListenerAccount;
use App\Application\Account\ListenerRepository;
use App\Application\Account\NewListener;

final class InMemoryListenerRepository implements ListenerRepository
{
    /**
     * @var array<int, array{name: string, email: string, password: string}>
     */
    private array $listeners = [];

    private int $nextId = 1;

    public function seed(string $name, string $email, string $passwordHash): ListenerAccount
    {
        $id = $this->nextId++;

        $this->listeners[$id] = [
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ];

        return new ListenerAccount($id, $name, $email);
    }

    public function existsByEmail(string $email): bool
    {
        foreach ($this->listeners as $listener) {
            if ($listener['email'] === $email) {
                return true;
            }
        }

        return false;
    }

    public function emailTakenByAnother(int $listenerId, string $email): bool
    {
        foreach ($this->listeners as $id => $listener) {
            if ($id !== $listenerId && $listener['email'] === $email) {
                return true;
            }
        }

        return false;
    }

    public function create(NewListener $listener): ListenerAccount
    {
        return $this->seed($listener->name, $listener->email, $listener->passwordHash);
    }

    public function updateProfile(int $listenerId, string $name, string $email): ListenerAccount
    {
        $this->listeners[$listenerId]['name'] = $name;
        $this->listeners[$listenerId]['email'] = $email;

        return new ListenerAccount($listenerId, $name, $email);
    }

    public function passwordHashFor(int $listenerId): ?string
    {
        return $this->listeners[$listenerId]['password'] ?? null;
    }

    public function updatePassword(int $listenerId, string $passwordHash): void
    {
        $this->listeners[$listenerId]['password'] = $passwordHash;
    }
}
