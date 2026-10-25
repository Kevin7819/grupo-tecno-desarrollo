<?php

namespace Tests\Unit\Application;

use App\Application\Account\EmailAlreadyRegistered;
use App\Application\Account\RegisterListener;
use App\Application\Account\RegisterListenerData;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakePasswordHasher;
use Tests\Support\InMemoryListenerRepository;

final class RegisterListenerTest extends TestCase
{
    public function test_it_creates_a_listener_with_normalized_data(): void
    {
        $listeners = new InMemoryListenerRepository;
        $register = new RegisterListener($listeners, new FakePasswordHasher);

        $account = $register->execute(new RegisterListenerData(
            name: '  Ana Pérez  ',
            email: '  ANA@Example.COM ',
            password: 'secret-password',
        ));

        $this->assertSame('Ana Pérez', $account->name);
        $this->assertSame('ana@example.com', $account->email);
        $this->assertSame('hashed:secret-password', $listeners->passwordHashFor($account->id));
    }

    public function test_it_never_stores_the_plain_password(): void
    {
        $listeners = new InMemoryListenerRepository;
        $register = new RegisterListener($listeners, new FakePasswordHasher);

        $account = $register->execute(new RegisterListenerData(
            name: 'Ana',
            email: 'ana@example.com',
            password: 'secret-password',
        ));

        $this->assertNotSame('secret-password', $listeners->passwordHashFor($account->id));
    }

    public function test_it_rejects_a_duplicate_email(): void
    {
        $listeners = new InMemoryListenerRepository;
        $listeners->seed('Ana', 'ana@example.com', 'hashed:whatever');
        $register = new RegisterListener($listeners, new FakePasswordHasher);

        $this->expectException(EmailAlreadyRegistered::class);

        $register->execute(new RegisterListenerData(
            name: 'Otra Ana',
            email: 'ana@example.com',
            password: 'secret-password',
        ));
    }

    public function test_it_rejects_a_duplicate_email_with_different_case_and_spacing(): void
    {
        $listeners = new InMemoryListenerRepository;
        $listeners->seed('Ana', 'ana@example.com', 'hashed:whatever');
        $register = new RegisterListener($listeners, new FakePasswordHasher);

        $this->expectException(EmailAlreadyRegistered::class);

        $register->execute(new RegisterListenerData(
            name: 'Otra Ana',
            email: '  ANA@EXAMPLE.COM  ',
            password: 'secret-password',
        ));
    }
}
