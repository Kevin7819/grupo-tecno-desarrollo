<?php

namespace App\Application\Admin;

use RuntimeException;

final class AdministratorNotRemovable extends RuntimeException
{
    public static function ownAccount(): self
    {
        return new self('No podés eliminar la cuenta con la que tenés la sesión iniciada.');
    }

    public static function lastAccount(): self
    {
        return new self('No podés eliminar la última cuenta administrativa: el panel quedaría sin acceso.');
    }
}
