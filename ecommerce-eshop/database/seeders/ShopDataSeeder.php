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
use App\Models\Offer;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ShopDataSeeder extends Seeder
{
    public function run(): void
    {
        // ===== 1) Admin & customers =====
        User::updateOrCreate(
            ['email' => 'admin@elegance.com'],
            [
                'name'     => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 1,
            ]
        );

        $customers = [
            ['name' => 'أحمد العمري',     'email' => 'ahmed@example.com'],
            ['name' => 'سارة القحطاني',   'email' => 'sara@example.com'],
            ['name' => 'محمد الحربي',     'email' => 'mohammed@example.com'],
            ['name' => 'نورة العتيبي',    'email' => 'noura@example.com'],
            ['name' => 'خالد الدوسري',    'email' => 'khaled@example.com'],
            ['name' => 'ريم الشمري',      'email' => 'reem@example.com'],
            ['name' => 'فهد المطيري',     'email' => 'fahd@example.com'],
        ];

        $userModels = [];
        foreach ($customers as $c) {
            $userModels[] = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name'     => $c['name'],
                    'password' => Hash::make('password123'),
                    'role'     => 'user',
                    'status'   => 1,
                ]
            );
        }

        // ===== 2) Categories, subcategories, and products =====
        $data = [
            // ---- نسائي ----
            [
                'name' => 'نسائي',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=800&auto=format&fit=crop',
                'subcategories' => [
                    ['name' => 'فساتين سهرة', 'products' => [
                        ['name' => 'فستان سهرة مرصع بالكريستال', 'price' => 1800, 'old_price' => 2400, 'image' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'فستان مخملي ملكي طويل',     'price' => 1200, 'old_price' => 1600, 'image' => 'https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'فستان كوكتيل أسود أنيق',     'price' => 950,  'old_price' => null, 'image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'فستان زفاف بحياكة يدوية',    'price' => 4500, 'old_price' => 5500, 'image' => 'https://images.unsplash.com/photo-1594472303328-c6e2dbfa3a18?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'عبايات وأزياء عربية', 'products' => [
                        ['name' => 'عباية مطرزة يدوياً بخيوط ذهبية', 'price' => 1200, 'old_price' => 1500, 'image' => 'https://images.unsplash.com/photo-1621112902351-4c4d41929bc7?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'قفطان مغربي فاخر',                'price' => 2800, 'old_price' => 3500, 'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'جلابية عصرية مزدوجة',             'price' => 750,  'old_price' => 900,  'image' => 'https://images.unsplash.com/photo-1551489186-cf8726f514f8?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'أحذية نسائية', 'products' => [
                        ['name' => 'حذاء كعب عالي من جلدالثعبان',   'price' => 650, 'old_price' => 850, 'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'صندل نسائي بتفاصيل ذهبية',       'price' => 480, 'old_price' => null,'image' => 'https://images.unsplash.com/photo-1562273138-f46be4ebdf33?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'حذاء كلاسيكي بيك اب أسود',       'price' => 550, 'old_price' => 700, 'image' => 'https://images.unsplash.com/photo-1535043934128-cf0b28d52f95?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],

            // ---- رجالي ----
            [
                'name' => 'رجالي',
                'image' => 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?q=80&w=800&auto=format&fit=crop',
                'subcategories' => [
                    ['name' => 'بدلات رسمية', 'products' => [
                        ['name' => 'بدلة إيطالية فاخرة بنفسجية', 'price' => 2500, 'old_price' => 3000, 'image' => 'https://images.unsplash.com/photo-1594938292227-4191bb31355f?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'بليزر كحلي عصري بخامة الصوف', 'price' => 850,  'old_price' => 1100, 'image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'بدلة كلاسيك رمادي فحمي',      'price' => 1750, 'old_price' => null,  'image' => 'https://images.unsplash.com/photo-1593032465175-481ac7f401a0?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'ملابس كاجوال', 'products' => [
                        ['name' => 'تيشيرت بولو كلاسيك قطني',     'price' => 180,  'old_price' => 250,  'image' => 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'جينز سليم فيت بلون غامق',      'price' => 320,  'old_price' => 420,  'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'قميص قطني بأكمام طويلة',        'price' => 240,  'old_price' => null, 'image' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'جاكيت جلد طبيعي بني',           'price' => 1450, 'old_price' => 1800, 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'أحذية رجالية', 'products' => [
                        ['name' => 'حذاء رسمي أكسفورد جلد',         'price' => 780, 'old_price' => 950, 'image' => 'https://images.unsplash.com/photo-1614253429340-98120bd6d753?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'سنيكرز رياضي عصري',              'price' => 540, 'old_price' => null,'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'حذاء لوفر بني كلاسيكي',          'price' => 620, 'old_price' => 750, 'image' => 'https://images.unsplash.com/photo-1582897085656-c636d006a246?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],

            // ---- إكسسوارات ----
            [
                'name' => 'إكسسوارات',
                'image' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=800&auto=format&fit=crop',
                'subcategories' => [
                    ['name' => 'ساعات', 'products' => [
                        ['name' => 'ساعة كلاسيكية مطلية بالذهب',     'price' => 600,  'old_price' => 1200, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'ساعة رجالية سويسرية فاخرة',     'price' => 3200, 'old_price' => 4000, 'image' => 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'ساعة نسائية بكريستال سواروفسكي', 'price' => 850,  'old_price' => 1100, 'image' => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'حقائب', 'products' => [
                        ['name' => 'حقيبة يد جلد طبيعي فاخرة',      'price' => 1200, 'old_price' => 1500, 'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'حقيبة كتف عصرية بنقشة فهد',     'price' => 880,  'old_price' => null,  'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'كلاتش سهرة مطرز',                'price' => 450,  'old_price' => 600,  'image' => 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?q=80&w=800&auto=format&fit=crop'],
                    ]],
                    ['name' => 'عطور', 'products' => [
                        ['name' => 'عطر العود الكمبودي الفاخر',      'price' => 1500, 'old_price' => 2000, 'image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'عطر مسك أبيض فاخر',              'price' => 680,  'old_price' => 850,  'image' => 'https://images.unsplash.com/photo-1523293182086-7651a8dd0f37?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'عطر زهري للنساء',                'price' => 520,  'old_price' => null, 'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],

            // ---- عروض حصرية ----
            [
                'name' => 'عروض حصرية',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=800&auto=format&fit=crop',
                'subcategories' => [
                    ['name' => 'تخفيضات الموسم', 'products' => [
                        ['name' => 'جاكيت شتوي فاخر',          'price' => 450,  'old_price' => 900,  'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'معطف صوف بقلبوس فرو',       'price' => 1100, 'old_price' => 2200, 'image' => 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'فستان صيفي بقطعة واحدة',     'price' => 280,  'old_price' => 560,  'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'حقيبة كروس بأحزمة ذهبية',     'price' => 380,  'old_price' => 760,  'image' => 'https://images.unsplash.com/photo-1591561954557-26941169b49e?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],
        ];

        $allProducts = [];
        $usedSlugs = [];

        foreach ($data as $catData) {
            $category = Category::updateOrCreate(
                ['name' => $catData['name']],
                [
                    'description' => 'تشكيلة مختارة من ' . $catData['name'],
                    'image'       => $catData['image'] ?? null,
                    'status'      => 1,
                ]
            );

            foreach ($catData['subcategories'] as $subData) {
                $subcategory = Subcategory::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $subData['name']],
                    ['status' => 1]
                );

                foreach ($subData['products'] as $prodData) {
                    $slug = Str::slug($prodData['name'] . '-' . substr(md5($prodData['name']), 0, 6));
                    $isOffer = $catData['name'] === 'عروض حصرية' || ($prodData['old_price'] && $prodData['old_price'] > $prodData['price']);

                    $discountPct = 0;
                    if ($prodData['old_price'] && $prodData['old_price'] > $prodData['price']) {
                        $discountPct = round((($prodData['old_price'] - $prodData['price']) / $prodData['old_price']) * 100);
                    }

                    $product = Product::updateOrCreate(
                        ['name' => $prodData['name']],
                        [
                            'subcategory_id'      => $subcategory->id,
                            'slug'                => $slug,
                            'description'         => "قطعة حصرية ومميزة من {$prodData['name']}. نجمع لك بين الجودة العالية والتصميم العصري لتناسب ذوقك الرفيع. مصنوعة من أجود الخامات لتدوم طويلاً.",
                            'short_description'   => "تصميم فاخر من {$prodData['name']} يجمع الأناقة والراحة.",
                            'price'               => $prodData['price'],
                            'old_price'           => $prodData['old_price'],
                            'total_stock'         => rand(50, 200),
                            'status'              => 1,
                            'image'               => $prodData['image'],
                            'is_featured'         => rand(0, 1),
                            'is_on_offer'         => $isOffer,
                            'discount_percentage' => $discountPct,
                            'sku'                 => strtoupper(substr(md5($prodData['name']), 0, 8)),
                        ]
                    );

                    $allProducts[] = $product;

                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'image_path' => $prodData['image']]
                    );

                    // Attributes
                    $colors = ['أسود', 'أبيض', 'كحلي', 'بيج', 'أحمر', 'رمادي'];
                    $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                    $colorSubset = array_intersect_key($colors, array_flip((array)array_rand($colors, 3)));
                    $sizeSubset  = array_intersect_key($sizes,  array_flip((array)array_rand($sizes, 3)));

                    foreach ($colorSubset as $color) {
                        foreach ($sizeSubset as $size) {
                            ProductAttribute::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'color'      => $color,
                                    'size'       => $size,
                                ],
                                ['qty' => rand(5, 25)]
                            );
                        }
                    }

                    // Reviews
                    $comments = [
                        'جودة رائعة جداً، أنصح به بشدة!',
                        'التوصيل كان سريع والمنتج مطابق للصور.',
                        'خامة ممتازة وتصميم أنيق وراقي.',
                        'القماش مريح جداً والمقاس مضبوط.',
                        'أفضل متجر تعاملت معه، شكراً لكم.',
                        'منتج فاخر يستحق كل ريال.',
                        'خدمة عملاء ممتازة ورد سريع على استفساراتي.',
                    ];

                    foreach (array_rand($userModels, 2) as $uIdx) {
                        Review::updateOrCreate(
                            [
                                'user_id'    => $userModels[$uIdx]->id,
                                'product_id' => $product->id,
                            ],
                            [
                                'rating'  => rand(4, 5),
                                'comment' => $comments[array_rand($comments)],
                            ]
                        );
                    }
                }
            }
        }

        // ===== 3) Sample orders =====
        foreach ($userModels as $idx => $user) {
            if ($idx >= 4) break; // 4 sample orders
            $order = Order::create([
                'user_id'         => $user->id,
                'order_number'    => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name'   => $user->name,
                'customer_email'  => $user->email,
                'customer_phone'  => '05' . rand(10000000, 99999999),
                'shipping_address'=> json_encode(['address' => 'حي الياسمين، الرياض', 'city' => 'الرياض', 'postal_code' => '12345']),
                'billing_address' => json_encode(['address' => 'حي الياسمين، الرياض', 'city' => 'الرياض', 'postal_code' => '12345']),
                'subtotal'        => 0,
                'tax'             => 0,
                'shipping_cost'   => 50,
                'total'           => 0,
                'currency_code'   => 'SAR',
                'status'          => ['pending', 'processing', 'delivered', 'completed'][rand(0, 3)],
                'payment_status'  => rand(0, 1) ? 'paid' : 'pending',
                'payment_method'  => 'cash_on_delivery',
            ]);

            $total = 0;
            $orderProds = array_intersect_key($allProducts, array_flip((array)array_rand($allProducts, rand(1, 3))));
            foreach ($orderProds as $prod) {
                $qty = rand(1, 2);
                $price = (float) $prod->price;
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $prod->id,
                    'product_name' => $prod->name,
                    'quantity'     => $qty,
                    'price'        => $price,
                    'unit_price'   => $price,
                    'total_price'  => $price * $qty,
                ]);
                $total += $price * $qty;
            }

            $tax = $total * 0.15;
            $order->update([
                'subtotal' => $total,
                'tax'      => $tax,
                'total'    => $total + $tax + 50,
            ]);
        }

        // ===== 4) Offers (for ticker and dashboard) =====
        Offer::updateOrCreate(
            ['name' => 'خصم الافتتاح الكبير'],
            [
                'discount_value' => 20,
                'type'           => 'percentage',
                'scope'          => 'all',
                'starts_at'      => now(),
                'ends_at'        => now()->addMonth(),
                'status'         => 1,
            ]
        );

        if (count($allProducts) > 0) {
            Offer::updateOrCreate(
                ['name' => 'تصفية على الجاكيتات'],
                [
                    'discount_value' => 50,
                    'type'           => 'percentage',
                    'scope'          => 'product',
                    'target_id'      => $allProducts[0]->id,
                    'starts_at'      => now()->subDays(5),
                    'ends_at'        => now()->addDays(15),
                    'status'         => 1,
                ]
            );
        }

        $cat = Category::first();
        if ($cat) {
            Offer::updateOrCreate(
                ['name' => 'عروض قسم ' . $cat->name],
                [
                    'discount_value' => 100,
                    'type'           => 'fixed',
                    'scope'          => 'category',
                    'target_id'      => $cat->id,
                    'starts_at'      => now(),
                    'ends_at'        => now()->addDays(10),
                    'status'         => 1,
                ]
            );
        }

        // ===== 5) Currencies =====
        Currency::updateOrCreate(['code' => 'SAR'], ['name' => 'ريال سعودي', 'symbol' => 'ر.س', 'exchange_rate' => 1.0,  'is_default' => true,  'status' => true]);
        Currency::updateOrCreate(['code' => 'USD'], ['name' => 'دولار أمريكي', 'symbol' => '$',   'exchange_rate' => 0.27, 'is_default' => false, 'status' => true]);
        Currency::updateOrCreate(['code' => 'AED'], ['name' => 'درهم إماراتي', 'symbol' => 'د.إ', 'exchange_rate' => 0.98, 'is_default' => false, 'status' => true]);
        Currency::updateOrCreate(['code' => 'KWD'], ['name' => 'دينار كويتي',   'symbol' => 'د.ك', 'exchange_rate' => 0.082,'is_default' => false, 'status' => true]);
        Currency::updateOrCreate(['code' => 'EGP'], ['name' => 'جنيه مصري',     'symbol' => 'ج.م', 'exchange_rate' => 12.5, 'is_default' => false, 'status' => true]);

        // ===== 6) Wishlist (random) =====
        foreach ($userModels as $user) {
            $wishProds = (array) array_rand($allProducts, rand(1, 3));
            foreach ($wishProds as $pIdx) {
                Wishlist::updateOrCreate(
                    ['user_id' => $user->id, 'product_id' => $allProducts[$pIdx]->id],
                    []
                );
            }
        }
    }
}
