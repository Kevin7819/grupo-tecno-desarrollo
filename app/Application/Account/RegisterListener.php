<?php

namespace App\Application\Account;

use App\Domain\Account\Email;

final readonly class RegisterListener
{
    public function __construct(
        private ListenerRepository $listeners,
        private PasswordHasher $hasher,
    ) {}

    public function execute(RegisterListenerData $data): ListenerAccount
    {
        $email = Email::fromString($data->email);

        if ($this->listeners->existsByEmail($email->value)) {
            throw EmailAlreadyRegistered::for($email->value);
        }

        return $this->listeners->create(new NewListener(
            name: trim($data->name),
            email: $email->value,
            passwordHash: $this->hasher->hash($data->password),
        ));
    }
}
