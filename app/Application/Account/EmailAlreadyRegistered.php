<?php

namespace App\Application\Account;

use RuntimeException;

final class EmailAlreadyRegistered extends RuntimeException
{
    public static function for(string $email): self
    {
        return new self("The email [{$email}] is already registered.");
    }
}
