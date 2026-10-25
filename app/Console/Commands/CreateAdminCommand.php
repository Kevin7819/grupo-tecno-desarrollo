<?php

namespace App\Console\Commands;

use App\Application\Admin\AdminAlreadyExists;
use App\Application\Admin\ProvisionAdmin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

final class CreateAdminCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'radio:create-admin
                            {--name= : Administrator name}
                            {--email= : Administrator email}';

    /**
     * @var string
     */
    protected $description = 'Create an administrator account for the radio panel';

    public function handle(ProvisionAdmin $provision): int
    {
        $name = trim((string) ($this->option('name') ?? $this->ask('Nombre del administrador')));
        $email = mb_strtolower(trim((string) ($this->option('email') ?? $this->ask('Correo electrónico'))));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'min:2', 'max:120'],
                'email' => ['required', 'string', 'email', 'max:255'],
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'name.min' => 'El nombre debe tener al menos 2 caracteres.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico no es válido.',
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = (string) $this->secret('Contraseña (mínimo 8 caracteres)');
        $confirmation = (string) $this->secret('Repite la contraseña');

        if (mb_strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        if ($password !== $confirmation) {
            $this->error('Las contraseñas no coinciden.');

            return self::FAILURE;
        }

        try {
            $admin = $provision->execute($name, $email, $password);
        } catch (AdminAlreadyExists) {
            $this->error('Ya existe un administrador con ese correo electrónico.');

            return self::FAILURE;
        }

        $this->info("Administrador creado: {$admin->email}");

        return self::SUCCESS;
    }
}
