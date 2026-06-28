<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('يجب وجود مستخدمين ومنتجات أولاً!');
            return;
        }

        // إنشاء 15 طلب عشوائي
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();

            // 1. إنشاء الطلب الرئيسي
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0, // سنحدثه بعد إضافة العناصر
                'status' => collect(['pending', 'completed', 'shipped'])->random(),
                'created_at' => now()->subDays(rand(1, 20)),
            ]);

            $orderTotal = 0;

            // 2. إضافة من 1 إلى 3 منتجات لكل طلب
            $selectedProducts = $products->random(min($products->count(), rand(1, 3)));
            foreach ($selectedProducts as $product) {
                $qty = rand(1, 2);

                // جلب سمة عشوائية للمنتج (لون ومقاس) ليكون الاختبار واقعياً
                $attribute = $product->attributes()->inRandomOrder()->first();

                $price = $product->price * $qty;
                $orderTotal += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'size' => $attribute ? $attribute->size : 'M',   // قيمة افتراضية إذا لم يوجد
                    'color' => $attribute ? $attribute->color : 'black', // قيمة افتراضية
                ]);
            }
            // تحديث إجمالي سعر الطلب
            $order->update(['total_price' => $orderTotal]);
        }

        $this->command->info('it`s done seeding orders!');
    }
}