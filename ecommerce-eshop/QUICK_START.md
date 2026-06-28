# 🚀 دليل البدء السريع

## المتطلبات الأساسية
- PHP 8.2+
- MySQL 5.7+
- Composer
- Node.js + npm

## خطوات التثبيت

### 1️⃣ التثبيت الأولي
```bash
# استنساخ المشروع
git clone <repository-url>
cd ecommerce-shop

# تثبيت الحزم PHP
composer install

# تثبيت حزم JavaScript
npm install
```

### 2️⃣ إعداد البيئة
```bash
# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# تحديث قاعدة البيانات في .env
# DB_DATABASE=ecommerce_shop
# DB_USERNAME=root
# DB_PASSWORD=
```

### 3️⃣ إعداد قاعدة البيانات
```bash
# تشغيل الـ Migrations
php artisan migrate

# إدراج البيانات الأساسية (اختياري)
php artisan db:seed

# أو استخدام أمر واحد
php artisan migrate:fresh --seed
```

### 4️⃣ إنشاء الروابط الرمزية
```bash
php artisan storage:link
```

### 5️⃣ تجميع الموارد
```bash
# للتطوير
npm run dev

# للإنتاج
npm run build
```

### 6️⃣ تشغيل الخادم
```bash
# في تاب منفصل
php artisan serve

# أو استخدام Vite للتطوير (مع npm run dev)
```

## 🌐 الوصول للمتجر

بعد تشغيل الخادم:
- **المتجر**: http://localhost:8000
- **قاعمة بيانات**: MySQL على localhost:3306
- **البريد المحلي**: http://localhost:8000/mails

## 📚 الملفات المهمة

| الملف | الوصف |
|------|-------|
| `.env` | إعدادات التطبيق والقاعدة |
| `routes/web.php` | المسارات الرئيسية |
| `app/Models/` | نماذج البيانات |
| `app/Services/` | خدمات الأعمال |
| `resources/views/` | قوالب Blade |
| `database/migrations/` | تحديثات قاعدة البيانات |

## 🎯 أول خطوات للتطوير

### 1. إنشاء مستخدم جديد
```bash
php artisan tinker
# User::factory()->create(['email' => 'test@example.com', 'password' => 'password']);
```

### 2. إضافة منتجات تجريبية
استخدم قاعمة البيانات وأدخل المنتجات مباشرة، أو:
```bash
php artisan db:seed --class=ProductSeeder
```

### 3. اختبار السلة والطلب
1. اذهب إلى http://localhost:8000
2. أضف منتجات للسلة
3. انتقل إلى الدفع
4. أملأ البيانات وأتمم الطلب

## 🛠️ أوامر مفيدة

```bash
# مسح الـ Cache
php artisan cache:clear
php artisan config:clear

# فحص المشاكل
php artisan doctor

# إنشاء Backup
php artisan backup:run

# إعادة تعيين قاعدة البيانات
php artisan migrate:refresh

# تشغيل الاختبارات
php artisan test
```

## 📦 البيئات

### التطوير (Development)
```bash
APP_ENV=development
APP_DEBUG=true
```

### الاختبار (Testing)
```bash
APP_ENV=testing
APP_DEBUG=true
DB_DATABASE=ecommerce_testing
```

### الإنتاج (Production)
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://youromain.com
```

## 🔐 الأمان قبل الإطلاق

- [ ] تغيير `APP_KEY`
- [ ] تعيين `APP_DEBUG=false`
- [ ] تحديث `DB_PASSWORD`
- [ ] إنشاء `MAIL_PASSWORD`
- [ ] إضافة مفاتيح الدفع
- [ ] تفعيل HTTPS
- [ ] تعيين `CORS` بشكل صحيح

## 🆘 استكشاف الأخطاء

### خطأ 500
```bash
# افحص السجلات
tail -f storage/logs/laravel.log

# أعد تعيين الأذونات
chmod -R 775 storage bootstrap/cache
```

### مشاكل قاعدة البيانات
```bash
# تحقق من الاتصال
php artisan migrate --step

# أعد البيانات
php artisan migrate:refresh --seed
```

### مشاكل الجلسات
```bash
# امسح الجلسات
php artisan session:table
php artisan migrate
```

## 📞 للمساعدة
- البريد: support@example.com
- الهاتف: +966123456789
- الموقع: https://example.com

---

شكراً لاستخدام Elegance Fashion! 🎉
