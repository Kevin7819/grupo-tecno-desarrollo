<?php

namespace App\Application\Account;

use RuntimeException;

final class InvalidCurrentPassword extends RuntimeException
{
    public static function create(): self
    {
        return new self('The current password does not match.');
    }
}
