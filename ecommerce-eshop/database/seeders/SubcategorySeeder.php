<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $men = Category::where('name', 'ملابس رجالية')->first();
        $women = Category::where('name', 'ملابس نسائية')->first();

        // أقسام فرعية للرجال
        $men_subs = ['قمصان', 'بناطيل', 'أحذية رجالية'];
        foreach ($men_subs as $sub) {
            Subcategory::create([
                'name' => $sub,
                'category_id' => $men->id,
                'status' => 1
            ]);
        }

        // أقسام فرعية للنساء
        $women_subs = ['تنانير', 'فساتين', 'أحذية نسائية', 'حقائب'];
        foreach ($women_subs as $sub) {
            Subcategory::create([
                'name' => $sub,
                'category_id' => $women->id,
                'status' => 1
            ]);
        }
        $this->command->info('it`s done seeding subcategories!');

    }
}
