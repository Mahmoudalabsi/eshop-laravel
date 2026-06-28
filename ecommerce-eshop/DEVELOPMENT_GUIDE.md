# 👨‍💻 دليل التطوير

هذا الدليل يساعدك على فهم البنية والعمل على المشروع.

## 🎯 المبادئ المعمارية

### 1. Service Layer Pattern
يتم فصل منطق الأعمال في طبقة الخدمات:
```php
// Controller
public function store(Request $request, OrderService $orderService)
{
    $order = $orderService->createFromCart($request->all());
}

// Service
class OrderService
{
    public function createFromCart($data) { ... }
}
```

### 2. Repository Pattern
كل Model لديها الخصائص والعلاقات اللازمة:
```php
class Product extends Model
{
    public function scopeActive($query) { ... }
    public function getDiscountedPriceAttribute() { ... }
}
```

### 3. Blade Templates
استخدام Blade للقوالب الديناميكية مع Bootstrap 5:
```blade
@forelse ($products as $product)
    <div class="card">{{ $product->name }}</div>
@empty
    <p>لا توجد منتجات</p>
@endforelse
```

## 📂 هيكل المشروع

```
ecommerce-shop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── OrderController.php
│   │   │   └── CheckoutController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── User.php
│   │   └── ...
│   ├── Services/
│   │   ├── ProductService.php
│   │   ├── CartService.php
│   │   └── OrderService.php
│   └── Providers/
│
├── routes/
│   ├── web.php          - مسارات الويب
│   ├── api.php          - مسارات الـ API
│   └── console.php      - أوامر Artisan
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── products/
│   │   ├── cart/
│   │   ├── checkout/
│   │   └── orders/
│   ├── css/
│   └── js/
│
├── database/
│   ├── migrations/      - تحديثات قاعدة البيانات
│   ├── seeders/         - بيانات وهمية
│   └── factories/       - مصانع البيانات
│
├── config/              - ملفات الإعدادات
├── storage/             - ملفات مؤقتة
├── public/              - ملفات عامة
└── tests/               - اختبارات
```

## 🔄 سير العمل

### إضافة منتج جديد
```
1. الإدارة تدخل بيانات المنتج
2. Controller تتحقق من البيانات
3. Service تقوم بحفظ المنتج
4. قاعدة البيانات تخزن البيانات
5. View تعرض قائمة المنتجات المحدثة
```

### عملية الطلب
```
1. المستخدم يضيف منتجات للسلة
2. CartService تحفظ البيانات في Session
3. المستخدم ينتقل للدفع
4. يملأ نموذج معلوماته
5. OrderService تنشئ الطلب والعناصر
6. Session تُمسح
7. يتم عرض صفحة النجاح
```

## 🛠️ أدوات التطوير

### أوامر Artisan مهمة

```bash
# إنشاء Migration
php artisan make:migration create_products_table

# تشغيل Migrations
php artisan migrate

# إنشاء Model مع Migration و Controller
php artisan make:model Product -mcr

# إنشاء Seeder
php artisan make:seeder ProductSeeder

# تشغيل Seeders
php artisan db:seed

# إنشاء Service
php artisan make:class Services/ProductService

# مسح Cache
php artisan cache:clear
php artisan config:clear
```

### اختبار الـ API

```bash
# استخدام Laravel Tinker
php artisan tinker

# مثال:
> Product::all()
> Product::where('status', 'active')->limit(5)->get()
```

## 📝 كتابة كود جيد

### 1. تسمية صحيحة
```php
// ✅ صحيح
public function getActiveProducts($limit = 10)
public function calculateTotalPrice()
public function validateOrderData()

// ❌ خطأ
public function get_products()
public function calc()
public function check()
```

### 2. التعليقات الواضحة
```php
/**
 * الحصول على المنتجات النشطة مع الفلاتر
 * 
 * @param array $params - معاملات الفلترة
 * @return Collection - مجموعة المنتجات
 */
public function getAll($params = [])
{
    // ... الكود
}
```

### 3. معالجة الأخطاء
```php
try {
    $order = $this->orderService->createFromCart($data);
    return redirect()->route('checkout.success', $order);
} catch (\Exception $e) {
    return redirect()->back()
                  ->with('error', $e->getMessage());
}
```

### 4. التحقق من البيانات
```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email',
    'phone' => 'required|regex:/^\d{10,}$/'
]);
```

## 🧪 الاختبار

### إنشاء اختبار
```bash
php artisan make:test ProductTest
```

### كتابة اختبار
```php
class ProductTest extends TestCase
{
    public function test_can_get_active_products()
    {
        $product = Product::factory()->create(['status' => 'active']);
        $products = Product::active()->get();
        $this->assertContains($product->id, $products->pluck('id'));
    }
}
```

### تشغيل الاختبارات
```bash
php artisan test
php artisan test --filter=ProductTest
```

## 🔍 تصحيح الأخطاء

### استخدام dd() و dump()
```php
// إيقاف التنفيذ وطباعة البيانات
dd($variable);

// فقط طباعة البيانات
dump($variable);
```

### استخدام Log
```php
use Illuminate\Support\Facades\Log;

Log::info('Product created', ['product_id' => $product->id]);
Log::error('Payment failed', ['error' => $e->getMessage()]);
```

### استخدام Debugbar
```php
// في ملف .env
DEBUGBAR_ENABLED=true

// سيظهر شريط في أسفل الصفحة بمعلومات التصحيح
```

## 🚀 نصائح الأداء

### 1. Eager Loading
```php
// ❌ N+1 Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->subcategory->name;
}

// ✅ Eager Loading
$products = Product::with('subcategory')->get();
```

### 2. Caching
```php
$products = Cache::remember('featured_products', 3600, function () {
    return Product::featured()->limit(8)->get();
});
```

### 3. Pagination
```php
// ✅ استخدام Pagination بدلاً من جلب الكل
$products = Product::paginate(15);
```

## 📚 موارد تعليمية

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Eloquent](https://laravel.com/docs/eloquent)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## ⚠️ تحذيرات هامة

1. **لا تستخدم Hardcoded Values**
   ```php
   // ❌ خطأ
   return view('products', ['price' => 100]);
   
   // ✅ صحيح
   return view('products', ['price' => config('shop.default_price')]);
   ```

2. **تحقق دائماً من الصلاحيات**
   ```php
   if ($order->user_id !== auth()->id()) {
       abort(403);
   }
   ```

3. **استخدم Transactions للعمليات المعقدة**
   ```php
   DB::transaction(function () {
       // عمليات متعددة
   });
   ```

## 📞 المساعدة

إذا واجهت مشكلة:
1. تحقق من رسالة الخطأ
2. ابحث في Laravel Docs
3. استخدم Google أو Stack Overflow
4. اطلب المساعدة من الفريق

---

Happy Coding! 🚀
