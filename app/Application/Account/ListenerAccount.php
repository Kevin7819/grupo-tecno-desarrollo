<?php

namespace App\Application\Account;

final readonly class ListenerAccount
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}
