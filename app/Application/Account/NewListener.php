<?php

namespace App\Application\Account;

final readonly class NewListener
{
    public function __construct(
        public string $name,
        public string $email,
        public string $passwordHash,
    ) {}
}
