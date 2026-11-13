<?php

namespace App\Application\Admin;

use App\Application\Account\PasswordHasher;
use App\Application\Cms\AdminActivityLogger;
use App\Application\Cms\AdminActor;
use App\Domain\Account\Email;

final readonly class UpdateAdministrator
{
    public function __construct(
        private AdminAccountRepository $admins,
        private PasswordHasher $hasher,
        private AdminActivityLogger $activity,
    ) {}

    public function handle(
        AdminAccount $account,
        string $name,
        string $email,
        ?string $plainPassword,
        AdminActor $actor,
    ): AdminAccount {
        $email = Email::fromString($email);

        if ($this->admins->existsByEmailExcept($email->value, $account->id)) {
            throw AdminAlreadyExists::for($email->value);
        }

        $password = trim((string) $plainPassword);

        if ($password !== '') {
            if (mb_strlen($password) < 8) {
                throw InvalidAdministratorPassword::tooShort();
            }

            $this->admins->updatePassword($account->id, $this->hasher->hash($password));
        }

        $updated = $this->admins->update($account, trim($name), $email->value);

        $this->activity->record($actor, 'administrator.updated', $updated->email);

        return $updated;
    }
}
