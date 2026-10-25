<?php

namespace Tests\Unit\Application;

use App\Application\Account\EmailAlreadyRegistered;
use App\Application\Account\UpdateListenerProfile;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryListenerRepository;

final class UpdateListenerProfileTest extends TestCase
{
    public function test_it_normalizes_the_name_and_email(): void
    {
        $listeners = new InMemoryListenerRepository;
        $account = $listeners->seed('Ana', 'ana@example.com', 'hashed:whatever');
        $update = new UpdateListenerProfile($listeners);

        $updated = $update->execute($account->id, '  Ana María  ', '  ANA.MARIA@Example.COM ');

        $this->assertSame('Ana María', $updated->name);
        $this->assertSame('ana.maria@example.com', $updated->email);
    }

    public function test_it_allows_keeping_the_same_email(): void
    {
        $listeners = new InMemoryListenerRepository;
        $account = $listeners->seed('Ana', 'ana@example.com', 'hashed:whatever');
        $update = new UpdateListenerProfile($listeners);

        $updated = $update->execute($account->id, 'Ana', 'ana@example.com');

        $this->assertSame('ana@example.com', $updated->email);
    }

    public function test_it_rejects_an_email_owned_by_another_listener(): void
    {
        $listeners = new InMemoryListenerRepository;
        $ana = $listeners->seed('Ana', 'ana@example.com', 'hashed:whatever');
        $listeners->seed('Beto', 'beto@example.com', 'hashed:whatever');
        $update = new UpdateListenerProfile($listeners);

        $this->expectException(EmailAlreadyRegistered::class);

        $update->execute($ana->id, 'Ana', 'beto@example.com');
    }
}
