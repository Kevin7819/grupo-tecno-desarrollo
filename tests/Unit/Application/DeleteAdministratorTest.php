<?php

namespace Tests\Unit\Application;

use App\Application\Admin\AdministratorNotRemovable;
use App\Application\Admin\DeleteAdministrator;
use App\Application\Cms\AdminActivityLogger;
use App\Application\Cms\AdminActor;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryAdminAccountRepository;

final class DeleteAdministratorTest extends TestCase
{
    public function test_the_last_remaining_account_cannot_be_deleted(): void
    {
        $admins = new InMemoryAdminAccountRepository;
        $account = $admins->create('Única Persona', 'unica@radio.test', 'hash');

        $delete = new DeleteAdministrator($admins, $this->logger());

        try {
            $delete->handle($account, 999, new AdminActor(999, 'Otra Persona'));
            $this->fail('The last administrator account was deleted.');
        } catch (AdministratorNotRemovable) {
            $this->assertSame(1, $admins->count());
        }
    }

    public function test_an_administrator_cannot_delete_their_own_account(): void
    {
        $admins = new InMemoryAdminAccountRepository;
        $account = $admins->create('Persona Actual', 'actual@radio.test', 'hash');
        $admins->create('Otra Persona', 'otra@radio.test', 'hash');

        $delete = new DeleteAdministrator($admins, $this->logger());

        try {
            $delete->handle($account, $account->id, new AdminActor($account->id, $account->name));
            $this->fail('The current administrator account was deleted.');
        } catch (AdministratorNotRemovable) {
            $this->assertSame(2, $admins->count());
        }
    }

    public function test_another_account_can_be_deleted(): void
    {
        $admins = new InMemoryAdminAccountRepository;
        $current = $admins->create('Persona Actual', 'actual@radio.test', 'hash');
        $other = $admins->create('Otra Persona', 'otra@radio.test', 'hash');

        $logger = $this->logger();
        $delete = new DeleteAdministrator($admins, $logger);

        $delete->handle($other, $current->id, new AdminActor($current->id, $current->name));

        $this->assertSame(1, $admins->count());
        $this->assertNull($admins->find($other->id));
        $this->assertSame(['administrator.deleted|otra@radio.test'], $logger->actions);
    }

    private function logger(): AdminActivityLogger
    {
        return new class implements AdminActivityLogger
        {
            /**
             * @var array<int, string>
             */
            public array $actions = [];

            public function record(AdminActor $actor, string $action, string $subject): void
            {
                $this->actions[] = $action.'|'.$subject;
            }
        };
    }
}
