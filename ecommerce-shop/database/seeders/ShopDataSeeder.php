<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ShopDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Customers
        $customers = [
            ['name' => 'أحمد العمري', 'email' => 'ahmed@example.com'],
            ['name' => 'سارة القحطاني', 'email' => 'sara@example.com'],
            ['name' => 'محمد الحربي', 'email' => 'mohammed@example.com'],
            ['name' => 'نورة العتيبي', 'email' => 'noura@example.com'],
            ['name' => 'خالد الدوسري', 'email' => 'khaled@example.com'],
        ];

        $userModels = [];
        foreach ($customers as $c) {
            $userModels[] = User::create([
                'name' => $c['name'],
                'email' => $c['email'],
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 1
            ]);
        }

        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@elegance.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 1
            ]
        );

        $data = [
            [
                'name' => 'عروض حصرية',
                'subcategories' => [
                    ['name' => 'تخفيضات الموسم', 'products' => [
                        ['name' => 'جاكيت شتوي فاخر', 'price' => 450, 'old_price' => 900, 'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'فستان مخملي ملكي', 'price' => 1200, 'old_price' => 2400, 'image' => 'https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'ساعة كلاسيكية مطلية', 'price' => 600, 'old_price' => 1200, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],
            [
                'name' => 'رجالي',
                'subcategories' => [
                    ['name' => 'بدلات رسمية', 'products' => [
                        ['name' => 'بدلة إيطالية فاخرة', 'price' => 2500, 'old_price' => 3000, 'image' => 'https://images.unsplash.com/photo-1594932224036-9c20533429bc?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'بليزر كحلي عصري', 'price' => 850, 'old_price' => 1100, 'image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'ملابس كاجوال', 'products' => [
                        ['name' => 'تيشيرت بولو كلاسيك', 'price' => 180, 'old_price' => 250, 'image' => 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],
            [
                'name' => 'نسائي',
                'subcategories' => [
                    ['name' => 'فساتين', 'products' => [
                        ['name' => 'فستان سهرة مرصع', 'price' => 1800, 'old_price' => 2200, 'image' => 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'عبايات وأزياء عربية', 'products' => [
                        ['name' => 'عباية مطرزة يدوياً', 'price' => 1200, 'old_price' => 1500, 'image' => 'https://images.unsplash.com/photo-1621112904887-419379ce6824?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'قفطان مغربي فاخر', 'price' => 2800, 'old_price' => 3500, 'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],
            [
                'name' => 'إكسسوارات',
                'subcategories' => [
                    ['name' => 'عطور', 'products' => [
                        ['name' => 'عطر العود الكمبودي', 'price' => 1500, 'old_price' => 2000, 'image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ]
        ];

        $allProducts = [];

        foreach ($data as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'status' => 1,
            ]);

            foreach ($catData['subcategories'] as $subData) {
                $subcategory = Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subData['name'],
                    'status' => 1,
                ]);

                foreach ($subData['products'] as $prodData) {
                    $product = Product::create([
                        'subcategory_id' => $subcategory->id,
                        'name' => $prodData['name'],
                        'description' => "قطعة حصرية ومميزة من " . $prodData['name'] . ". نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.",
                        'price' => $prodData['price'],
                        'old_price' => $prodData['old_price'],
                        'total_stock' => rand(50, 200),
                        'status' => 1,
                        'image' => $prodData['image'],
                    ]);

                    $allProducts[] = $product;

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $prodData['image'],
                    ]);

                    // Attributes
                    $colors = ['أسود', 'أبيض', 'كحلي', 'أحمر'];
                    $sizes = ['S', 'M', 'L', 'XL'];

                    foreach (array_intersect_key($colors, array_flip((array)array_rand($colors, 2))) as $color) {
                        foreach (array_intersect_key($sizes, array_flip((array)array_rand($sizes, 2))) as $size) {
                            ProductAttribute::create([
                                'product_id' => $product->id,
                                'color' => $color,
                                'size' => $size,
                                'qty' => rand(10, 30)
                            ]);
                        }
                    }

                    // 4. Create Reviews
                    $comments = [
                        'جودة رائعة جداً، أنصح به!',
                        'التوصيل كان سريع والمنتج مطابق للصور.',
                        'خامة ممتازة وتصميم أنيق.',
                        'القماش مريح جداً والمقاس مضبوط.',
                        'أفضل متجر تعاملت معه، شكراً لكم.'
                    ];

                    foreach (array_rand($userModels, 2) as $uIdx) {
                        Review::create([
                            'user_id' => $userModels[$uIdx]->id,
                            'product_id' => $product->id,
                            'rating' => rand(4, 5),
                            'comment' => $comments[array_rand($comments)]
                        ]);
                    }
                }
            }
        }

        // 5. Create Orders
        foreach ($userModels as $user) {
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'phone' => '05' . rand(10000000, 99999999),
                'address' => 'المملكة العربية السعودية، الرياض، حي الياسمين',
                'total_price' => 0,
                'status' => 'pending', // or 'completed', 'shipping'
            ]);

            $total = 0;
            $orderProds = array_intersect_key($allProducts, array_flip((array)array_rand($allProducts, rand(1, 3))));
            
            foreach ($orderProds as $prod) {
                $qty = rand(1, 2);
                $price = $prod->price * $qty;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'price' => $prod->price,
                ]);
                $total += $price;
            }

            $order->update(['total_price' => $total, 'status' => rand(0,1) ? 'completed' : 'pending']);
        }

        // 6. Create Offers for the Dashboard Management Page
        \App\Models\Offer::create([
            'name' => 'خصم الافتتاح الكبير',
            'discount_value' => 20,
            'type' => 'percentage',
            'scope' => 'all',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 1
        ]);

        if (count($allProducts) > 0) {
            \App\Models\Offer::create([
                'name' => 'تصفية على الجاكيتات',
                'discount_value' => 50,
                'type' => 'percentage',
                'scope' => 'product',
                'target_id' => $allProducts[0]->id,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addDays(15),
                'status' => 1
            ]);
        }

        $cat = Category::first();
        if ($cat) {
            \App\Models\Offer::create([
                'name' => 'عروض قسم الـ ' . $cat->name,
                'discount_value' => 100,
                'type' => 'fixed',
                'scope' => 'category',
                'target_id' => $cat->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays(10),
                'status' => 1
            ]);
        }

        // 7. Create Currencies
        Currency::create(['name' => 'ريال سعودي', 'code' => 'SAR', 'symbol' => 'ر.س', 'exchange_rate' => 1.0, 'is_default' => true, 'status' => true]);
        Currency::create(['name' => 'دولار أمريكي', 'code' => 'USD', 'symbol' => '$', 'exchange_rate' => 0.27, 'is_default' => false, 'status' => true]);
        Currency::create(['name' => 'درهم إماراتي', 'code' => 'AED', 'symbol' => 'د.إ', 'exchange_rate' => 0.98, 'is_default' => false, 'status' => true]);
        Currency::create(['name' => 'دينار كويتي', 'code' => 'KWD', 'symbol' => 'د.ك', 'exchange_rate' => 0.082, 'is_default' => false, 'status' => true]);
    }
}
