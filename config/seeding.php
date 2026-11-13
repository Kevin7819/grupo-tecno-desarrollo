<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Development seeding
    |--------------------------------------------------------------------------
    |
    | Seeders for listener and administrator accounts are development helpers.
    | They run only in the environments listed here, and only when the
    | credentials below are explicitly configured. A missing password means
    | "do not seed" — the seeders never invent or default a password.
    |
    | A deployment that genuinely needs seeded accounts must add its own
    | environment to this list through configuration; it is never the
    | default behaviour.
    |
    */

    'allowed_environments' => ['local'],

    'admin' => [
        'name' => env('ADMIN_SEED_NAME', 'Administrador de desarrollo'),
        'email' => env('ADMIN_SEED_EMAIL'),
        'password' => env('ADMIN_SEED_PASSWORD'),
    ],

    'listeners' => [
        'password' => env('SEED_USER_PASSWORD'),
    ],

];
