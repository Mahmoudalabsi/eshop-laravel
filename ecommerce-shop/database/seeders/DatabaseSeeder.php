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
        // 1. إنشاء المستخدم المسؤول (Admin)
        User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@elegance.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        // 2. تشغيل سييدر اللغات
        $this->call([
            LanguageSeeder::class,
        ]);

        // 3. تشغيل سييدر البيانات الموحد للموضة
        $this->call([
            ShopDataSeeder::class,
        ]);
    }
}
