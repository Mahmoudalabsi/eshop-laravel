<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // جلب المنتجات والمستخدمين
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('تنبيه: يجب تشغيل UserSeeder و ProductSeeder أولاً!');
            return;
        }

        $comments = [
            'منتج رائع جداً وأنصح به',
            'الجودة ممتازة مقارنة بالسعر',
            'وصل في وقت قياسي، شكراً لكم',
            'التغليف جيد والمنتج أصلي',
            'تجربة شرائية جيدة جداً',
            'لم يعجبني كثيراً، الجودة متوسطة',
            'خدمة العملاء كانت متعاونة بخصوص هذا المنتج',
            'المقاس جاء مضبوطاً تماماً',
            'اللون مطابق للصورة تماماً'
        ];

        foreach ($products as $product) {
            // توليد من 1 إلى 4 تقييمات لكل منتج
            $reviewCount = rand(1, 4);

            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $users->random()->id,
                    'rating' => rand(3, 5), // معظم التقييمات إيجابية للمظهر العام
                    'comment' => $comments[array_rand($comments)],
                    'created_at' => now()->subDays(rand(1, 20))->subHours(rand(1, 23)),
                ]);
            }
        }

        $this->command->info('it`s done seeding reviews!');
    }
}
