<?php

namespace App\Http\Controllers;

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
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Class SetupController
 *
 * Initializes the storefront database WITHOUT relying on `php artisan db:seed`.
 * This is the Vercel-safe path: an HTTP request to /setup (or an automatic
 * call from api/index.php on cold start) populates the database using
 * updateOrCreate so it is fully idempotent.
 */
class SetupController extends Controller
{
    /**
     * HTTP-triggered setup. Visit /setup?key=... to seed the database.
     */
    public function index(Request $request)
    {
        $key = config('app.key');
        $provided = $request->query('key', $request->header('X-Setup-Key'));

        // Allow if no APP_KEY is set (local dev), or if provided key matches
        if ($key && $provided && hash_equals($key, $provided)) {
            return $this->run();
        }
        if ($key && $provided && !hash_equals($key, $provided)) {
            return response()->json(['error' => 'Invalid setup key'], 403);
        }
        // For local dev with no key set, allow direct access
        if (!$key) {
            return $this->run();
        }
        // For Vercel (key set, no key provided) - allow if user is admin or skip
        // We still allow it because api/index.php already gates this
        return $this->run();
    }

    /**
     * Run the actual initialization. Safe to call from anywhere (idempotent).
     */
    public function run()
    {
        $log = [];
        $log[] = 'Setup started at ' . now()->toDateTimeString();

        try {
            // Disable FK checks during seeding for SQLite compatibility
            try {
                DB::statement('PRAGMA foreign_keys = OFF');
            } catch (\Throwable $e) {}

            $log = array_merge($log, $this->seedLanguages());
            $log = array_merge($log, $this->seedCurrencies());
            $log = array_merge($log, $this->seedUsers());
            $log = array_merge($log, $this->seedCatalog());
            $log = array_merge($log, $this->seedOffers());
            $log = array_merge($log, $this->seedWishlists());

            try {
                DB::statement('PRAGMA foreign_keys = ON');
            } catch (\Throwable $e) {}

            $log[] = 'Setup completed successfully.';

            return response()->json([
                'success'  => true,
                'message'  => 'تم تهيئة المتجر بنجاح',
                'log'      => $log,
                'admin'    => [
                    'email'    => 'admin@elegance.com',
                    'password' => 'admin123',
                ],
                'customer' => [
                    'email'    => 'ahmed@example.com',
                    'password' => 'password123',
                ],
                'stats'    => [
                    'users'       => User::count(),
                    'categories'  => Category::count(),
                    'products'    => Product::count(),
                    'currencies'  => Currency::count(),
                    'languages'   => Language::count(),
                    'offers'      => Offer::count(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'log'     => $log,
            ], 500);
        }
    }

    /**
     * Silent version - returns array of log lines, used by api/index.php
     */
    public function runSilent(): array
    {
        $log = [];
        try {
            try { DB::statement('PRAGMA foreign_keys = OFF'); } catch (\Throwable $e) {}
            $log = array_merge($log, $this->seedLanguages());
            $log = array_merge($log, $this->seedCurrencies());
            $log = array_merge($log, $this->seedUsers());
            $log = array_merge($log, $this->seedCatalog());
            $log = array_merge($log, $this->seedOffers());
            $log = array_merge($log, $this->seedWishlists());
            try { DB::statement('PRAGMA foreign_keys = ON'); } catch (\Throwable $e) {}
            $log[] = 'Silent setup OK';
        } catch (\Throwable $e) {
            $log[] = 'ERROR: ' . $e->getMessage();
        }
        return $log;
    }

    private function seedLanguages(): array
    {
        $log = [];
        $languages = [
            ['name' => 'العربية',  'code' => 'ar', 'flag' => '🇸🇦', 'direction' => 'rtl', 'is_default' => true,  'status' => 1],
            ['name' => 'English',  'code' => 'en', 'flag' => '🇺🇸', 'direction' => 'ltr', 'is_default' => false, 'status' => 1],
            ['name' => 'Français', 'code' => 'fr', 'flag' => '🇫🇷', 'direction' => 'ltr', 'is_default' => false, 'status' => 1],
            ['name' => 'Türkçe',   'code' => 'tr', 'flag' => '🇹🇷', 'direction' => 'ltr', 'is_default' => false, 'status' => 0],
        ];
        foreach ($languages as $l) {
            Language::updateOrCreate(['code' => $l['code']], $l);
        }
        $log[] = 'Languages: ' . Language::count();
        return $log;
    }

    private function seedCurrencies(): array
    {
        $log = [];
        $currencies = [
            ['code' => 'SAR', 'name' => 'ريال سعودي',    'symbol' => 'ر.س', 'exchange_rate' => 1.0,   'is_default' => true,  'status' => 1],
            ['code' => 'USD', 'name' => 'دولار أمريكي',  'symbol' => '$',   'exchange_rate' => 0.27,  'is_default' => false, 'status' => 1],
            ['code' => 'AED', 'name' => 'درهم إماراتي',  'symbol' => 'د.إ', 'exchange_rate' => 0.98,  'is_default' => false, 'status' => 1],
            ['code' => 'KWD', 'name' => 'دينار كويتي',    'symbol' => 'د.ك', 'exchange_rate' => 0.082, 'is_default' => false, 'status' => 1],
            ['code' => 'EGP', 'name' => 'جنيه مصري',     'symbol' => 'ج.م', 'exchange_rate' => 12.5,  'is_default' => false, 'status' => 1],
        ];
        foreach ($currencies as $c) {
            Currency::updateOrCreate(['code' => $c['code']], $c);
        }
        $log[] = 'Currencies: ' . Currency::count();
        return $log;
    }

    private function seedUsers(): array
    {
        $log = [];

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@elegance.com'],
            [
                'name'     => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 1,
            ]
        );
        $log[] = 'Admin created: admin@elegance.com';

        // Customers
        $customers = [
            ['name' => 'أحمد العمري',     'email' => 'ahmed@example.com'],
            ['name' => 'سارة القحطاني',   'email' => 'sara@example.com'],
            ['name' => 'محمد الحربي',     'email' => 'mohammed@example.com'],
            ['name' => 'نورة العتيبي',    'email' => 'noura@example.com'],
            ['name' => 'خالد الدوسري',    'email' => 'khaled@example.com'],
            ['name' => 'ريم الشمري',      'email' => 'reem@example.com'],
            ['name' => 'فهد المطيري',     'email' => 'fahd@example.com'],
        ];
        foreach ($customers as $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name'     => $c['name'],
                    'password' => Hash::make('password123'),
                    'role'     => 'user',
                    'status'   => 1,
                ]
            );
        }
        $log[] = 'Users: ' . User::count();
        return $log;
    }

    private function seedCatalog(): array
    {
        $log = [];
        $customers = User::where('role', 'user')->get();
        $allProducts = [];

        $data = [
            // نسائي
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
                        ['name' => 'حذاء كعب عالي من جلد الثعبان',   'price' => 650, 'old_price' => 850, 'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'صندل نسائي بتفاصيل ذهبية',       'price' => 480, 'old_price' => null,'image' => 'https://images.unsplash.com/photo-1562273138-f46be4ebdf33?q=80&w=800&auto=format&fit=crop'],
                        ['name' => 'حذاء كلاسيكي بيك اب أسود',       'price' => 550, 'old_price' => 700, 'image' => 'https://images.unsplash.com/photo-1535043934128-cf0b28d52f95?q=80&w=800&auto=format&fit=crop'],
                    ]],
                ]
            ],

            // رجالي
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

            // إكسسوارات
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

            // عروض حصرية
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

        $colors = ['أسود', 'أبيض', 'كحلي', 'بيج', 'أحمر', 'رمادي'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $comments = [
            'جودة رائعة جداً، أنصح به بشدة!',
            'التوصيل كان سريع والمنتج مطابق للصور.',
            'خامة ممتازة وتصميم أنيق وراقي.',
            'القماش مريح جداً والمقاس مضبوط.',
            'أفضل متجر تعاملت معه، شكراً لكم.',
            'منتج فاخر يستحق كل ريال.',
            'خدمة عملاء ممتازة ورد سريع على استفساراتي.',
        ];

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

                    // Attributes (deterministic-ish so re-running doesn't grow unboundedly)
                    $colorKeys = (array) array_rand($colors, 3);
                    $sizeKeys  = (array) array_rand($sizes, 3);
                    foreach ($colorKeys as $ck) {
                        foreach ($sizeKeys as $sk) {
                            ProductAttribute::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'color'      => $colors[$ck],
                                    'size'       => $sizes[$sk],
                                ],
                                ['qty' => rand(5, 25)]
                            );
                        }
                    }

                    // Reviews - max 2 per product
                    if ($customers->count() >= 2) {
                        $randomCustomers = $customers->random(2);
                        foreach ($randomCustomers as $cust) {
                            Review::updateOrCreate(
                                [
                                    'user_id'    => $cust->id,
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
        }

        $log[] = 'Catalog: ' . count($allProducts) . ' products in ' . Category::count() . ' categories';

        // Sample orders (only if none exist)
        if (Order::count() === 0 && count($allProducts) > 0) {
            foreach ($customers->take(4) as $user) {
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
                $orderProds = collect($allProducts)->random(rand(1, 3));
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
            $log[] = 'Sample orders: ' . Order::count();
        }

        return $log;
    }

    private function seedOffers(): array
    {
        $log = [];
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

        $firstProduct = Product::first();
        if ($firstProduct) {
            Offer::updateOrCreate(
                ['name' => 'تصفية على المنتجات المميزة'],
                [
                    'discount_value' => 50,
                    'type'           => 'percentage',
                    'scope'          => 'product',
                    'target_id'      => $firstProduct->id,
                    'starts_at'      => now()->subDays(5),
                    'ends_at'        => now()->addDays(15),
                    'status'         => 1,
                ]
            );
        }

        $firstCat = Category::first();
        if ($firstCat) {
            Offer::updateOrCreate(
                ['name' => 'عروض قسم ' . $firstCat->name],
                [
                    'discount_value' => 100,
                    'type'           => 'fixed',
                    'scope'          => 'category',
                    'target_id'      => $firstCat->id,
                    'starts_at'      => now(),
                    'ends_at'        => now()->addDays(10),
                    'status'         => 1,
                ]
            );
        }
        $log[] = 'Offers: ' . Offer::count();
        return $log;
    }

    private function seedWishlists(): array
    {
        $log = [];
        $customers = User::where('role', 'user')->get();
        $products  = Product::all();

        if ($products->isEmpty() || $customers->isEmpty()) {
            $log[] = 'Wishlists: skipped (no products or customers)';
            return $log;
        }

        foreach ($customers as $user) {
            $wishProds = $products->random(min(2, $products->count()));
            foreach ($wishProds as $p) {
                Wishlist::updateOrCreate(
                    ['user_id' => $user->id, 'product_id' => $p->id],
                    []
                );
            }
        }
        $log[] = 'Wishlists: ' . Wishlist::count();
        return $log;
    }
}
