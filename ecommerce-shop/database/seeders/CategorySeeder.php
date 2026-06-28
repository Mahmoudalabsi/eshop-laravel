<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'ملابس رجالية',
                'description' => 'تشكيلة راقية من القمصان، السراويل، والبدل الرجالية',
                'status' => 1
            ],
            [
                'name' => 'ملابس نسائية',
                'description' => 'أحدث صيحات الموضة النسائية والفساتين',
                'status' => 1
            ],
            [
                'name' => 'ملابس أطفال',
                'description' => 'ملابس مريحة وعصرية للأولاد والبنات بكافة الأعمار',
                'status' => 1
            ],
            [
                'name' => 'أحذية',
                'description' => 'حقائب، أحذية، وأحزمة جلدية لإكمال مظهرك',
                'status' => 1
            ]
        ];

        foreach ($categories as $cat) {
            // استخدمنا updateOrCreate لتجنب تكرار البيانات إذا شغلت Seeder مرة أخرى
            Category::updateOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description'], 'status' => $cat['status']]
            );
        }
        $this->command->info('it`s done seeding categories!');

    }
}