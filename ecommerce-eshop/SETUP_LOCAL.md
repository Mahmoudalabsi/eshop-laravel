# Elegance Fashion - متجر فاخر متكامل

متجر إلكتروني فاخر للأزياء مبني على Laravel 12 + SQLite (لا حاجة لقاعدة بيانات خارجية أو API).

## المميزات

- تصميم فاخر بطابع كحلي + ذهبي موحّد لكامل الموقع
- واجهة متجر كاملة: رئيسية، منتجات، فئات، عروض، بحث حيّ
- سلة تسوق تعمل بالجلسات (session) — لا حاجة لتسجيل دخول
- قائمة المفضلة (Wishlist) — تتطلب تسجيل دخول
- نظام طلبات كامل: إتمام الشراء، الفاتورة، إلغاء الطلب
- لوحة تحكم للأدمن: لغات، أدلة مقاسات، فئات
- بيانات وهمية جاهزة: فئات، منتجات، صور، تقييمات، عملات، عروض، طلبات، مفضلة
- حساب أدمن جاهز للاستخدام

## التشغيل المحلي

### 1) المتطلبات
- PHP 8.2+ مع إضافات: mbstring, xml, sqlite3, curl, zip
- Composer

### 2) التثبيت

```bash
cd ecommerce-eshop
composer install --no-dev --optimize-autoloader  # أو composer install للتطوير
cp .env.example .env  # عدّل القيم حسب الحاجة
php artisan key:generate
```

ملف `.env` الحالي مضبوط مسبقاً على SQLite محلي:

```
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/ecommerce-eshop/database/database.sqlite
```

### 3) تشغيل المايغريشن + Seeders (إنشاء قاعدة البيانات وملئها)

```bash
php artisan migrate:fresh --seed
```

هذا سينشئ:
- جميع الجداول (users, categories, products, orders, wishlists, offers, currencies, languages ...)
- **حساب الأدمن**: `admin@elegance.com` / `admin123`
- 7 عملاء تجريبيين
- 4 فئات رئيسية + أقسام فرعية + ~30 منتج بصور حقيقية من Unsplash
- تقييمات لكل منتج
- 4 طلبات تجريبية
- 3 عروض نشطة
- 5 عملات (SAR, USD, AED, KWD, EGP)
- 3 لغات (ar, en, fr)
- مفضلة لكل عميل

### 4) تشغيل السيرفر

```bash
php artisan serve --port=8001
```

افتح المتصفح على: http://localhost:8001

## حسابات تجريبية

| النوع    | البريد               | كلمة المرور   |
|----------|----------------------|---------------|
| أدمن     | admin@elegance.com   | admin123      |
| عميل     | ahmed@example.com    | password123   |
| عميل     | sara@example.com     | password123   |

## المسارات الرئيسية

| المسار                  | الوصف                              |
|-------------------------|------------------------------------|
| `/`                     | الصفحة الرئيسية                    |
| `/products`             | كل المنتجات (مع فلترة وبحث)         |
| `/categories/{id}`      | منتجات قسم محدد                    |
| `/offers`               | صفحة العروض                        |
| `/cart`                 | سلة التسوق                         |
| `/checkout`             | إتمام الشراء (يتطلب دخول)           |
| `/wishlist`             | المفضلة (تتطلب دخول)                |
| `/orders`               | طلباتي (يتطلب دخول)                 |
| `/profile`              | الملف الشخصي (يتطلب دخول)           |
| `/login` & `/register`  | الدخول والتسجيل                    |
| `/admin/languages`      | إدارة اللغات (يتطلب أدمن)           |
| `/admin/size-guides`    | إدارة أدلة المقاسات (يتطلب أدمن)    |
| `/admin/categories`     | إدارة الفئات (يتطلب أدمن)           |

## ملاحظات تقنية

- المتجر **مستقل تماماً** عن لوحة التحكم `ecommerce-shop` — لا يستدعي أي API خارجي
- جميع البيانات تأتي من قاعدة SQLite المحلية عبر نماذج Eloquent
- السلة تعمل بالـ Session (لا حاجة لتسجيل دخول لإضافة منتجات)
- الطلبات وقائمة المفضلة مرتبطة بحساب المستخدم
- يعمل على Vercel أيضاً عبر نفس الإعداد (انظر `vercel.json`)

## إعادة التهيئة الكاملة

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan serve --port=8001
```
