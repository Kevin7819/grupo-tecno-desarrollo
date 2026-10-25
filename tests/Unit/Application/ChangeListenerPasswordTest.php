<?php

namespace Tests\Unit\Application;

use App\Application\Account\ChangeListenerPassword;
use App\Application\Account\InvalidCurrentPassword;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakePasswordHasher;
use Tests\Support\InMemoryListenerRepository;

final class ChangeListenerPasswordTest extends TestCase
{
    public function test_it_replaces_the_password_hash_when_the_current_password_matches(): void
    {
        $listeners = new InMemoryListenerRepository;
        $account = $listeners->seed('Ana', 'ana@example.com', 'hashed:current-password');
        $change = new ChangeListenerPassword($listeners, new FakePasswordHasher);

        $change->execute($account->id, 'current-password', 'new-password');

        $this->assertSame('hashed:new-password', $listeners->passwordHashFor($account->id));
    }

    public function test_it_rejects_a_wrong_current_password_without_touching_the_hash(): void
    {
        $listeners = new InMemoryListenerRepository;
        $account = $listeners->seed('Ana', 'ana@example.com', 'hashed:current-password');
        $change = new ChangeListenerPassword($listeners, new FakePasswordHasher);

        try {
            $change->execute($account->id, 'wrong-password', 'new-password');
            $this->fail('A wrong current password must be rejected.');
        } catch (InvalidCurrentPassword) {
            $this->assertSame('hashed:current-password', $listeners->passwordHashFor($account->id));
        }
    }

    public function test_it_rejects_an_unknown_listener(): void
    {
        $listeners = new InMemoryListenerRepository;
        $change = new ChangeListenerPassword($listeners, new FakePasswordHasher);

        $this->expectException(InvalidCurrentPassword::class);

        $change->execute(999, 'current-password', 'new-password');
    }
}
