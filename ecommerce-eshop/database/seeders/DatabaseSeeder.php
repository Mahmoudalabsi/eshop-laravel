<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Languages
        $this->call([
            LanguageSeeder::class,
        ]);

        // 2. Admin + customers + categories + products + offers + currencies + orders + wishlists
        $this->call([
            ShopDataSeeder::class,
        ]);
    }
}
