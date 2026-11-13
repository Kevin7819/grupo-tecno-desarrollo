<?php

namespace App\Application\Admin;

use App\Application\Cms\AdminActivityLogger;
use App\Application\Cms\AdminActor;

final readonly class DeleteAdministrator
{
    public function __construct(
        private AdminAccountRepository $admins,
        private AdminActivityLogger $activity,
    ) {}

    public function handle(AdminAccount $account, int $actorId, AdminActor $actor): void
    {
        if ($account->id === $actorId) {
            throw AdministratorNotRemovable::ownAccount();
        }

        if ($this->admins->count() <= 1) {
            throw AdministratorNotRemovable::lastAccount();
        }

        $this->admins->delete($account);

        $this->activity->record($actor, 'administrator.deleted', $account->email);
    }
}
