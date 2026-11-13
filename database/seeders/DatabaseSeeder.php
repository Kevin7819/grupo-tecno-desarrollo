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
     * Both seeders are development helpers that gate themselves: they run
     * only in the environments listed in `config/seeding.php` and only when
     * their credentials are configured. In any other situation they skip,
     * so `db:seed` never creates an account by accident.
     *
     * Production administrators are still provisioned with
     * `php artisan radio:create-admin`.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
