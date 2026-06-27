<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('languages')->insert([
            [
                'name' => 'العربية',
                'code' => 'ar',
                'flag' => '🇸🇦',
                'direction' => 'rtl',
                'is_default' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'English',
                'code' => 'en',
                'flag' => '🇺🇸',
                'direction' => 'ltr',
                'is_default' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Français',
                'code' => 'fr',
                'flag' => '🇫🇷',
                'direction' => 'ltr',
                'is_default' => false,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
