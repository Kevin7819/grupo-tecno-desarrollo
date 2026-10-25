<?php

namespace App\Application\Admin;

use RuntimeException;

final class AdminAlreadyExists extends RuntimeException
{
    public static function for(string $email): self
    {
        return new self("An administrator with the email [{$email}] already exists.");
    }
}
