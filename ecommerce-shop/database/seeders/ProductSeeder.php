<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. الأقسام
        $men = Category::firstOrCreate(['name' => 'ملابس رجالية']);
        $women = Category::firstOrCreate(['name' => 'ملابس نسائية']);

        // 2. الأقسام الفرعية
        $shirts = Subcategory::firstOrCreate(
            ['name' => 'قمصان'],
            ['category_id' => $men->id, 'status' => 1]
        );

        $dresses = Subcategory::firstOrCreate(
            ['name' => 'فساتين'],
            ['category_id' => $women->id, 'status' => 1]
        );

        // 3. إنشاء منتج رجالي مع مواصفاته (ألوان ومقاسات)
        $shirtProduct = Product::create([
            'name' => 'قميص أكسفورد كلاسيك',
            'description' => 'قميص قطني أبيض مريح للعمل',
            'price' => 150.00,
            'status' => 1,
            'subcategory_id' => $shirts->id,
        ]);

        // إضافة مقاسات وألوان لهذا القميص
        $shirtProduct->attributes()->createMany([
            ['size' => 'L', 'color' => 'white', 'qty' => 10],
            ['size' => 'XL', 'color' => 'blue', 'qty' => 5],
        ]);

        // 4. إنشاء منتج نسائي مع مواصفاته
        $dressProduct = Product::create([
            'name' => 'فستان صيفي منقوش',
            'description' => 'فستان خفيف مخصص للأجواء المشمسة',
            'price' => 320.00,
            'status' => 1,
            'subcategory_id' => $dresses->id,
        ]);

        $dressProduct->attributes()->createMany([
            ['size' => 'S', 'color' => 'red', 'qty' => 8],
            ['size' => 'M', 'color' => 'red', 'qty' => 12],
        ]);

        $this->command->info('it`s done seeding products with attributes!');
    }
}