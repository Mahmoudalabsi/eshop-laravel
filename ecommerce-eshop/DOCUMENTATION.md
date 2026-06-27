# 🏪 Elegance Fashion - متجر الكتروني متقدم

متجر إلكتروني شامل مبني بـ Laravel 12 مع تصميم حديث وواجهة مستخدم رائعة.

## 🚀 المميزات

### النظام الأساسي
- ✅ نظام إدارة منتجات متقدم مع صور متعددة
- ✅ نظام تصنيفات وفئات فرعية
- ✅ نظام الطلبات الكامل مع متتبع الحالات
- ✅ سلة التسوق المتقدمة
- ✅ نظام الدفع والفواتير
- ✅ نظام التقييمات والتعليقات

### الميزات المتقدمة
- 🔄 دعم العملات المتعددة
- 🏷️ نظام الخصومات والعروض
- 📦 نظام الشحن والتتبع
- ⭐ نظام التقييمات والتقيم
- 🔍 بحث متقدم وفلترة المنتجات
- 📱 تصميم متجاوب (Responsive)
- 🌙 نظام ألوان احترافي

## 🏗️ البنية المعمارية

### Models (النماذج)
```
Product.php           - نموذج المنتج
Order.php            - نموذج الطلب
OrderItem.php        - نموذج عناصر الطلب
Review.php           - نموذج التقييمات
Rating.php           - نموذج النجمات
Category.php         - نموذج الفئات
Subcategory.php      - نموذج الفئات الفرعية
ProductVariant.php   - نموذج متغيرات المنتج
ProductImage.php     - نموذج صور المنتج
Transaction.php      - نموذج المعاملات المالية
Shipment.php         - نموذج الشحنات
Currency.php         - نموذج العملات
```

### Services (خدمات الأعمال)
```
ProductService.php   - خدمة إدارة المنتجات والبحث
CartService.php      - خدمة إدارة السلة
OrderService.php     - خدمة إدارة الطلبات
```

### Controllers (المتحكمات)
```
ProductController.php    - التحكم في المنتجات
CartController.php       - التحكم في السلة
OrderController.php      - التحكم في الطلبات
CheckoutController.php   - التحكم في عملية الدفع
```

## 📋 جداول قاعدة البيانات

### الجداول الرئيسية

#### `products`
```sql
- id (PK)
- subcategory_id (FK)
- name, slug, description
- price, old_price, cost_price
- image, sku, barcode
- total_stock, weight
- is_featured, is_on_offer
- discount_percentage
- status (active/inactive)
```

#### `orders`
```sql
- id (PK)
- user_id (FK)
- order_number (unique)
- status (pending/processing/shipped/delivered/cancelled)
- payment_status (paid/unpaid)
- subtotal, tax, shipping_cost, total
- currency_code
- customer_name, customer_email, customer_phone
- shipping_address (JSON)
- billing_address (JSON)
- payment_method, tracking_number
```

#### `order_items`
```sql
- id (PK)
- order_id (FK)
- product_id (FK)
- product_name, quantity, unit_price, total_price
- attributes (JSON)
```

#### `currencies`
```sql
- id (PK)
- code (SAR, USD, EUR)
- name, symbol
- exchange_rate
- is_primary
```

## 🔧 الإعدادات

### ملف `.env`
```env
APP_NAME="Elegance Fashion"
APP_ENV=production
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_shop
DB_USERNAME=root
DB_PASSWORD=

STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
```

## 📦 الحزم المستخدمة

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pint": "^1.24",
    "phpunit/phpunit": "^11.5.3"
  }
}
```

## 🎨 الواجهة الأمامية

### Technologies
- Bootstrap 5.3
- jQuery
- Font Awesome 6.4
- Google Fonts (Tajawal)

### الصفحات الرئيسية
- ✅ الرئيسية
- ✅ قائمة المنتجات
- ✅ تفاصيل المنتج
- ✅ السلة
- ✅ الدفع
- ✅ الطلبات
- ✅ تفاصيل الطلب

## 📍 المسارات الرئيسية

### المستخدم العام
```
GET  /                          - الصفحة الرئيسية
GET  /products                  - قائمة المنتجات
GET  /products/{id}             - تفاصيل المنتج
GET  /products/search?q=        - البحث
GET  /categories                - الفئات
GET  /offers                    - العروض
GET  /cart                      - السلة
```

### المستخدم المسجل
```
POST /cart/add/{id}             - إضافة للسلة
PATCH /cart/update              - تحديث السلة
DELETE /cart/remove/{id}        - حذف من السلة
GET  /checkout                  - صفحة الدفع
POST /checkout                  - إتمام الطلب
GET  /orders                    - قائمة الطلبات
GET  /orders/{id}               - تفاصيل الطلب
POST /orders/{id}/cancel        - إلغاء الطلب
```

## 🔐 الأمان

- ✅ CSRF Protection
- ✅ SQL Injection Prevention
- ✅ XSS Protection
- ✅ Authentication & Authorization
- ✅ Password Hashing (bcrypt)
- ✅ Session Management

## 📊 الخدمات المتوفرة

### ProductService
```php
getAll($params)          - الحصول على جميع المنتجات مع الفلاتر
find($id)                - البحث عن منتج
getFeatured($limit)      - المنتجات المميزة
getOnOffer($limit)       - المنتجات على العرض
getRelated($id)          - المنتجات ذات الصلة
search($query)           - البحث عن منتج
getByCategory($catId)    - المنتجات حسب الفئة
```

### CartService
```php
add($productId, $qty)    - إضافة منتج للسلة
remove($productId)       - حذف منتج من السلة
update($productId, $qty) - تحديث الكمية
get()                    - الحصول على السلة
getTotal()               - حساب الإجمالي
count()                  - عدد العناصر
clear()                  - مسح السلة
validateStock()          - التحقق من المخزون
```

### OrderService
```php
createFromCart($userId, $data)   - إنشاء طلب من السلة
getOrder($id)                    - الحصول على تفاصيل الطلب
getUserOrders($userId)           - طلبات المستخدم
updateStatus($orderId, $status)  - تحديث حالة الطلب
getStatistics()                  - إحصائيات الطلبات
```

## 🚀 البدء السريع

### التثبيت
```bash
# استنساخ المشروع
git clone <repo-url>
cd ecommerce-shop

# تثبيت الحزم
composer install
npm install

# إنشاء ملف .env
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# إعداد قاعدة البيانات
php artisan migrate --seed

# تجميع الموارد
npm run build
```

### التشغيل
```bash
# خادم التطوير
php artisan serve

# أو استخدام الأمر المتقدم
npm run dev
```

## 📝 أمثلة على الاستخدام

### إضافة منتج للسلة
```php
$cartService = app(CartService::class);
$cartService->add(1, 2, ['color' => 'red', 'size' => 'M']);
```

### إنشاء طلب
```php
$orderService = app(OrderService::class);
$order = $orderService->createFromCart(auth()->id(), [
    'customer_name' => 'أحمد محمد',
    'customer_email' => 'ahmed@example.com',
    'customer_phone' => '0501234567',
    'shipping_address' => 'شارع النيل، جدة',
    'city' => 'جدة',
    'postal_code' => '21421'
]);
```

### البحث عن منتج
```php
$productService = app(ProductService::class);
$products = $productService->search('قميص', 20);
```

## 🎯 الخطوات التالية

- [ ] نظام الدفع الإلكترونية (Stripe/Payfort)
- [ ] لوحة تحكم الإدارة
- [ ] نظام الرسائل النصية
- [ ] نظام الكوبونات والبطاقات الهدية
- [ ] نظام المراجعات المتقدم
- [ ] API RESTful
- [ ] تطبيق موبايل
- [ ] نظام التوصيات الذكية

## 📞 الدعم

للمساعدة أو الإبلاغ عن مشاكل، يرجى التواصل عبر:
- البريد الإلكتروني: support@example.com
- الهاتف: +966 1 2345 6789

## 📄 الترخيص

MIT License - جميع الحقوق محفوظة © 2024

---

تم بناء هذا المتجر بعناية وحب 💚 لتقديم أفضل تجربة تسوق إلكترونية.
