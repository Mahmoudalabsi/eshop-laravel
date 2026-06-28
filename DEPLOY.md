# 📦 دليل النشر الكامل (DEPLOY.md)

هذا الدليل يشرح كيفية نشر متجر **Elegance Fashion** (تطبيقان Laravel) على Vercel وبدائله المجانية.

---

## 🎯 الخلاصة

| المنصة | يدعم Laravel | قاعدة بيانات مجانية | سهولة | التوصية |
|--------|-------------|---------------------|--------|---------|
| Vercel | ⚠️ عبر runtime مجتمعي | ❌ (تحتاج خارجية) | متوسطة | نعم — مع Aiven MySQL |
| Render.com | ✅ رسمياً | ✅ 90 يوم | سهلة جداً | بديل ممتاز |
| Railway | ✅ رسمياً | ✅ $5 شهرياً | سهلة جداً | بديل سريع |
| Fly.io | ✅ عبر Dockerfile | ⚠️ محدود | متوسطة | للأداء العالمي |

---

## ☁️ الخيار 1: Vercel + Aiven MySQL (موصى به لأنك تملك توكن Vercel)

### المرحلة 1: إنشاء قاعدة بيانات MySQL سحابية

1. سجّل في https://aiven.io (مجاني 5GB)
2. أنشئ خدمة MySQL جديدة (Free plan)
3. احفظ:
   - `Host` (مثل `mysql-xxx.aivencloud.com`)
   - `Port` (عادة 12345 أو 3306)
   - `Database name` (افتراضياً `defaultdb`)
   - `User` (افتراضياً `avnadmin`)
   - `Password`

### المرحلة 2: ربط المستودع بـ Vercel

1. ادخل https://vercel.com/new
2. اختر مستودع `Mahmoudalabsi/eshop-laravel`
3. **كرّر هذه الخطوات لكل من التطبيقين:**

#### أ) مشروع Backend (ecommerce-shop):
- **Project Name**: `elegance-fashion-api`
- **Root Directory**: `ecommerce-shop`
- **Framework Preset**: Other
- **Build Command**: `npm install && npm run build`
- **Output Directory**: `public`
- **Install Command**: `composer install --no-dev --prefer-dist`

#### ب) مشروع Frontend (ecommerce-eshop):
- **Project Name**: `elegance-fashion-store`
- **Root Directory**: `ecommerce-eshop`
- **Framework Preset**: Other
- **Build Command**: `npm install && npm run build`
- **Output Directory**: `public`
- **Install Command**: `composer install --no-dev --prefer-dist`

### المرحلة 3: متغيرات البيئة

في Vercel → Settings → Environment Variables، أضف لكل مشروع:

```
APP_KEY=base64:XXXX          # نفّذ php artisan key:generate محلياً واحفظ القيمة
APP_NAME=Elegance Fashion API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://elegance-fashion-api.vercel.app
DB_CONNECTION=mysql
DB_HOST=mysql-xxx.aivencloud.com
DB_PORT=12345
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=your_password
SESSION_DRIVER=cookie
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stderr
```

للـ Frontend فقط، أضف أيضاً:
```
API_BASE_URL=https://elegance-fashion-api.vercel.app/api/v1
```

### المرحلة 4: تشغيل Migrations

من جهازك المحلي:
```bash
cd ecommerce-shop
# عدّل .env محلياً ليشير إلى MySQL السحابي
php artisan migrate --force
php artisan db:seed --force
```

### المرحلة 5: تحديث APP_URL

بعد النشر الأول، حدّث `APP_URL` و `API_BASE_URL` في Vercel ليطابق الدومين الفعلي.

---

## 🆘 الخيار 2: Render.com (أسهل — يدعم Laravel أصلياً)

### الخطوات

1. ادخل https://render.com → **New** → **Web Service**
2. اربط مستودع GitHub `Mahmoudalabsi/eshop-laravel`
3. الإعدادات:
   - **Name**: `elegance-fashion-api`
   - **Root Directory**: `ecommerce-shop`
   - **Environment**: PHP
   - **Build Command**:
     ```bash
     composer install --no-dev --prefer-dist
     npm install && npm run build
     php artisan key:generate
     php artisan migrate --force
     php artisan storage:link
     ```
   - **Start Command**:
     ```bash
     php artisan serve --host 0.0.0.0 --port $PORT
     ```
4. أضف MySQL addon: **New** → **MySQL** (Free: 90 يوم، ثم $7/شهر)
5. اربط متغيرات `DB_*` من قاعدة Render MySQL
6. كرّر لـ `ecommerce-eshop` مع `Root Directory: ecommerce-eshop`

---

## 🆘 الخيار 3: Railway.app

1. https://railway.app → **New Project** → **Deploy from GitHub repo**
2. اختر `Mahmoudalabsi/eshop-laravel`
3. **Settings → Root Directory**: `ecommerce-shop`
4. أضف **MySQL** من قالب Railway (Service → Add → MySQL)
5. Railway سيضبط `MYSQLHOST`, `MYSQLPORT`, ... تلقائياً
6. عدّل `DB_*` لاستخدام هذه المتغيرات
7. Build Command:
   ```bash
   composer install --no-dev && npm install && npm run build
   ```
8. Start Command:
   ```bash
   php artisan migrate --force && php artisan serve --host 0.0.0.0 --port $PORT
   ```

---

## ✅ التحقق من النشر

بعد نجاح النشر، اختبر:

```bash
# Backend API
curl https://your-api.vercel.app/api/v1/products

# Frontend
curl https://your-store.vercel.app/
```

ستحصل على JSON من الأول و HTML من الثاني.

---

## 🐛 مشاكل شائعة وحلولها

### 1) `No application encryption key has been specified`
أضف `APP_KEY` في Vercel → Environment Variables (بصيغة `base64:...`).

### 2) `SQLSTATE[HY000] [2002] Connection refused`
- تأكد أن مزود MySQL يسمح بالاتصالات من `0.0.0.0/0` (IP whitelist).
- في Aiven: Allowed IP Addresses → Add `0.0.0.0/0`.

### 3) `The stream or file "storage/logs/laravel.log" could not be opened`
في Vercel، ضع `LOG_CHANNEL=stderr` (موجود في vercel.json).

### 4) `419 Page Expired` عند تسجيل الدخول
- استخدم `SESSION_DRIVER=cookie` (موجود في vercel.json).
- تأكد أن `SESSION_DOMAIN` يطابق الدومين.

### 5) الصور لا تظهر
- Vercel storage مؤقت — استخدم S3:
  - `FILESYSTEM_DISK=s3`
  - `AWS_ACCESS_KEY_ID=...`
  - `AWS_SECRET_ACCESS_KEY=...`
  - `AWS_BUCKET=...`
  - `AWS_DEFAULT_REGION=us-east-1`

### 6) `php artisan migrate` لا يعمل على Vercel
Vercel serverless — لا يمكن تشغيل artisan أثناء النشر. شغّل migrations من جهازك المحلي ضد MySQL السحابي.

---

## 📞 الدعم

- استخدم `tail -f storage/logs/laravel.log` محلياً لتتبع الأخطاء.
- على Vercel: Dashboard → Project → Functions → Logs.
- على Render: Dashboard → Service → Logs.

---

**آخر تحديث**: يناسب Laravel 12 + PHP 8.2+.
