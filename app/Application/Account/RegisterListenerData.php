<?php

namespace App\Application\Account;

final readonly class RegisterListenerData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
