<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * NOTE: This seeder is intentionally empty.
 *
 * Both ecommerce-shop and ecommerce-eshop share the same MySQL database.
 * All migrations and seeders are run by ecommerce-shop (the Backend).
 * This seeder exists only to satisfy Laravel's structure expectations.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // No seeding here. Run `php artisan db:seed` inside ecommerce-shop instead.
    }
}
