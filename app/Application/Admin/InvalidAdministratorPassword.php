<?php

namespace App\Application\Admin;

use RuntimeException;

final class InvalidAdministratorPassword extends RuntimeException
{
    public static function tooShort(): self
    {
        return new self('La contraseña debe tener al menos 8 caracteres.');
    }
}
