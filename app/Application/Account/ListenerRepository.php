<?php

namespace App\Application\Account;

interface ListenerRepository
{
    public function existsByEmail(string $email): bool;

    public function emailTakenByAnother(int $listenerId, string $email): bool;

    public function create(NewListener $listener): ListenerAccount;

    public function updateProfile(int $listenerId, string $name, string $email): ListenerAccount;

    public function passwordHashFor(int $listenerId): ?string;

    public function updatePassword(int $listenerId, string $passwordHash): void;
}
