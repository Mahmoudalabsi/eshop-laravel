<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['name' => 'العربية',  'code' => 'ar', 'flag' => '🇸🇦', 'direction' => 'rtl', 'is_default' => true,  'status' => true],
            ['name' => 'English',  'code' => 'en', 'flag' => '🇺🇸', 'direction' => 'ltr', 'is_default' => false, 'status' => true],
            ['name' => 'Français', 'code' => 'fr', 'flag' => '🇫🇷', 'direction' => 'ltr', 'is_default' => false, 'status' => false],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
