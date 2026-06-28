<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Admin user (idempotent)
        User::updateOrCreate(
            ['email' => 'admin@elegance.com'],
            [
                'name'     => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 1,
            ]
        );

        // 2) Languages
        $this->call([
            LanguageSeeder::class,
        ]);

        // 3) Categories, products, customers, offers, currencies
        $this->call([
            ShopDataSeeder::class,
        ]);
    }
}
