<?php

namespace App\Application\Admin;

use App\Application\Account\PasswordHasher;
use App\Application\Cms\AdminActivityLogger;
use App\Application\Cms\AdminActor;
use App\Domain\Account\Email;

final readonly class CreateAdministrator
{
    public function __construct(
        private AdminAccountRepository $admins,
        private PasswordHasher $hasher,
        private AdminActivityLogger $activity,
    ) {}

    public function handle(string $name, string $email, string $plainPassword, AdminActor $actor): AdminAccount
    {
        $email = Email::fromString($email);

        if (mb_strlen($plainPassword) < 8) {
            throw InvalidAdministratorPassword::tooShort();
        }

        if ($this->admins->existsByEmail($email->value)) {
            throw AdminAlreadyExists::for($email->value);
        }

        $account = $this->admins->create(
            name: trim($name),
            email: $email->value,
            passwordHash: $this->hasher->hash($plainPassword),
        );

        $this->activity->record($actor, 'administrator.created', $account->email);

        return $account;
    }
}
