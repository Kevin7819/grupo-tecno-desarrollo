<?php

namespace App\Application\Account;

final readonly class ChangeListenerPassword
{
    public function __construct(
        private ListenerRepository $listeners,
        private PasswordHasher $hasher,
    ) {}

    public function execute(int $listenerId, string $currentPassword, string $newPassword): void
    {
        $currentHash = $this->listeners->passwordHashFor($listenerId);

        if ($currentHash === null || ! $this->hasher->verify($currentPassword, $currentHash)) {
            throw InvalidCurrentPassword::create();
        }

        $this->listeners->updatePassword($listenerId, $this->hasher->hash($newPassword));
    }
}
