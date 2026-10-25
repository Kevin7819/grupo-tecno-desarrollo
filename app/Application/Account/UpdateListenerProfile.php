<?php

namespace App\Application\Account;

use App\Domain\Account\Email;

final readonly class UpdateListenerProfile
{
    public function __construct(private ListenerRepository $listeners) {}

    public function execute(int $listenerId, string $name, string $email): ListenerAccount
    {
        $email = Email::fromString($email);

        if ($this->listeners->emailTakenByAnother($listenerId, $email->value)) {
            throw EmailAlreadyRegistered::for($email->value);
        }

        return $this->listeners->updateProfile($listenerId, trim($name), $email->value);
    }
}
