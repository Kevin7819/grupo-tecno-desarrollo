<?php

namespace Tests\Unit\Application;

use App\Application\Admin\AdminAlreadyExists;
use App\Application\Admin\ProvisionAdmin;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakePasswordHasher;
use Tests\Support\InMemoryAdminAccountRepository;

final class ProvisionAdminTest extends TestCase
{
    public function test_it_creates_an_administrator_with_hashed_password(): void
    {
        $admins = new InMemoryAdminAccountRepository;
        $provision = new ProvisionAdmin($admins, new FakePasswordHasher);

        $admin = $provision->execute('  Root  ', '  ROOT@Example.COM ', 'strong-password');

        $this->assertSame('Root', $admin->name);
        $this->assertSame('root@example.com', $admin->email);
        $this->assertSame('hashed:strong-password', $admins->passwordHashFor('root@example.com'));
        $this->assertNotSame('strong-password', $admins->passwordHashFor('root@example.com'));
    }

    public function test_it_rejects_a_duplicate_administrator(): void
    {
        $admins = new InMemoryAdminAccountRepository;
        $admins->create('Root', 'root@example.com', 'hashed:whatever');
        $provision = new ProvisionAdmin($admins, new FakePasswordHasher);

        $this->expectException(AdminAlreadyExists::class);

        $provision->execute('Otro Root', 'root@example.com', 'strong-password');
    }
}
