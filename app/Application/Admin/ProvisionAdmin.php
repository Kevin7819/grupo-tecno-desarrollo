<?php

namespace App\Application\Admin;

use App\Application\Account\PasswordHasher;
use App\Domain\Account\Email;

final readonly class ProvisionAdmin
{
    public function __construct(
        private AdminAccountRepository $admins,
        private PasswordHasher $hasher,
    ) {}

    public function execute(string $name, string $email, string $plainPassword): AdminAccount
    {
        $email = Email::fromString($email);

        if ($this->admins->existsByEmail($email->value)) {
            throw AdminAlreadyExists::for($email->value);
        }

        return $this->admins->create(
            name: trim($name),
            email: $email->value,
            passwordHash: $this->hasher->hash($plainPassword),
        );
    }
}
