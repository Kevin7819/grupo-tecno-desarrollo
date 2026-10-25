<?php

namespace App\Application\Admin;

final readonly class AdminAccount
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}
