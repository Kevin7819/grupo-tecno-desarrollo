<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Intentionally empty: production administrators are created with
     * `php artisan radio:create-admin`, never with a public default password.
     */
    public function run(): void
    {
        // No default users or administrators.
    }
}
