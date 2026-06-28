# دليل النشر الكامل على Vercel — متجر Elegance Fashion

> **الهدف**: نشر مشروعين منفصلين على Vercel، لكل منهما دومين خاص، مربوطين عبر API.
>
> - **الخلفية (ecommerce-shop)**: `https://elegance-fashion-api.vercel.app` — يقدم `/api/v1/*`
> - **الواجهة (ecommerce-eshop)**: `https://elegance-fashion-store.vercel.app` — يستدعي API الخلفية

---

## 📋 ما تم إنجازه في الكود

### 1) ملفات النشر لكل مشروع
| الملف | الوظيفة |
|------|---------|
| `ecommerce-shop/vercel.json` | إعدادات Vercel للمشروع الخلفي |
| `ecommerce-shop/vercel-build.sh` | سكربت البناء (composer + npm + artisan cache) |
| `ecommerce-shop/api/index.php` | نقطة دخول Laravel على Vercel serverless |
| `ecommerce-shop/.vercelignore` | ملفات مستثناة من الرفع |
| `ecommerce-eshop/vercel.json` | إعدادات Vercel للواجهة الأمامية + `API_BASE_URL` |
| `ecommerce-eshop/vercel-build.sh` | نفس سكربت البناء |
| `ecommerce-eshop/api/index.php` | نقطة دخول Laravel على Vercel serverless |
| `ecommerce-eshop/.vercelignore` | ملفات مستثناة |

### 2) كيف يعمل الربط عبر API
- في `ecommerce-eshop/config/api.php` يُقرأ `API_BASE_URL` من `.env`
- في `ecommerce-eshop/app/Services/ApiService.php` يُستخدم `Http::timeout()` للاتصال
- في `vercel.json` للواجهة الأمامية تم تثبيت:
  ```json
  "API_BASE_URL": "https://elegance-fashion-api.vercel.app/api/v1"
  ```
- في `ecommerce-shop/config/cors.php` الـ CORS مفتوح لجميع النطاقات (`'*'`)

### 3) ما تم إصلاحه في الكود سابقاً (14 خطأ برمجي)
جميع الأخطاء الـ 14 المذكورة في `/home/z/my-project/code-review.md` تم إصلاحها في الكوميت `6701995`، ومنها:
- ترحيلات مكررة، نموذج العملة،`placeOrder` API، `CartController` (tax/final_total)
- إزالة ملفات الـ debug، إصلاح `Storage` facade، إصلاح `AppLayout`/`GuestLayout`
- إصلاح `Sanctum token` (withToken يضيف Bearer تلقائياً)

---

## 🚀 خطوات النشر على Vercel

### الخطوة 1: إعداد قاعدة بيانات MySQL سحابية مجانية

Vercel لا يوفر قاعدة بيانات. سنستخدم **TiDB Cloud** (مجاني 5GB، متوافق 100% مع MySQL).

1. اذهب إلى: https://tidbcloud.com/free-trial
2. سجّل حساباً جديداً (يمكن استخدام GitHub/Google)
3. اختر **Serverless Tier** (المجاني بالكامل)
4. اختر المنطقة `Frankfurt (eu-central-1)` (الأقرب لخوادم Vercel)
5. بعد إنشاء الكلاستر، اضغط على **Connect** ثم **Overview**
6. سترى إعدادات الاتصال تشبه:
   ```
   Host:    gateway01.eu-central-1.prod.aws.tidbcloud.com
   Port:    4000
   User:    xxxxx.root
   Password: ********
   ```
7. اضغط على **Generate TLS Certificate** ثم نزّل `ca.pem` (لا نحتاجه لـ Laravel لأنه يتصل عبر TLS تلقائياً)
8. أنشئ قاعدة بيانات باسم `elegance_shop`:
   ```sql
   CREATE DATABASE elegance_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

**احفظ هذه القيم في مكان آمن، سنستخدمها لاحقاً:**
- `DB_HOST` = gateway01.eu-central-1.prod.aws.tidbcloud.com
- `DB_PORT` = 4000
- `DB_DATABASE` = elegance_shop
- `DB_USERNAME` = xxxxx.root
- `DB_PASSWORD` = كلمة المرور التي اخترتها

> **بديل TiDB**: Aiven (https://aiven.io) يوفر MySQL مجاني لمدة شهر، أو PlanetScale القديم (لم يعد مجانياً). TiDB هو الخيار الأفضل حالياً.

---

### الخطوة 2: توليد APP_KEY

شغّل هذا الأمر محلياً واحفظ الناتج:

```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

أو إذا لم يكن PHP مثبتاً محلياً:

```bash
node -e "console.log('base64:' + Buffer.from(crypto.randomBytes(32)).toString('base64'))"
```

ستحصل على شيء مثل: `base64:W4YZkqSXcK7F2qN3vJ8Xy5b1pTqj6xKQn8vLz9yW0aM=`

> **مهم**: استخدم نفس `APP_KEY` في كلا المشروعين لتعمل الجلسات بشكل صحيح بين الواجهة والخلفية إذا لزم الأمر.

---

### الخطوة 3: استيراد المشروع الأول (الخلفية) على Vercel

1. اذهب إلى: https://vercel.com/new
2. اختر مستودع GitHub: **Mahmoudalabsi/eshop-laravel**
3. في صفحة الإعدادات:
   - **Project Name**: `elegance-fashion-api`
   - **Framework Preset**: اتركه Other
   - **Root Directory**: اضغط Edit واختر `ecommerce-shop`
   - **Build Command**: اترك الافتراضي (سيقرأه من `vercel.json`)
   - **Output Directory**: اتركه (سيقرأ `public` من `vercel.json`)
4. في **Environment Variables**، أضف المتغيرات التالية:

| Key | Value |
|-----|-------|
| `APP_KEY` | (الـ APP_KEY الذي توليده في الخطوة 2) |
| `APP_URL` | `https://elegance-fashion-api.vercel.app` |
| `DB_HOST` | (TiDB Host) |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `elegance_shop` |
| `DB_USERNAME` | (TiDB User) |
| `DB_PASSWORD` | (TiDB Password) |
| `DB_CONNECTION` | `mysql` |

5. اضغط **Deploy** وانتظر اكتمال البناء (3-5 دقائق).
6. ستحصل على الرابط: `https://elegance-fashion-api.vercel.app`

---

### الخطوة 4: استيراد المشروع الثاني (الواجهة الأمامية) على Vercel

1. اذهب مرة أخرى إلى: https://vercel.com/new
2. اختر نفس المستودع: **Mahmoudalabsi/eshop-laravel**
3. في صفحة الإعدادات:
   - **Project Name**: `elegance-fashion-store`
   - **Framework Preset**: Other
   - **Root Directory**: اضغط Edit واختر `ecommerce-eshop`
4. في **Environment Variables**، أضف:

| Key | Value |
|-----|-------|
| `APP_KEY` | (نفس APP_KEY الذي استخدمته في الخطوة 3) |
| `APP_URL` | `https://elegance-fashion-store.vercel.app` |
| `API_BASE_URL` | `https://elegance-fashion-api.vercel.app/api/v1` |
| `API_TIMEOUT` | `15` |
| `DB_HOST` | (نفس TiDB Host) |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `elegance_shop` |
| `DB_USERNAME` | (نفس TiDB User) |
| `DB_PASSWORD` | (نفس TiDB Password) |
| `DB_CONNECTION` | `mysql` |

5. اضغط **Deploy** وانتظر البناء.
6. ستحصل على الرابط: `https://elegance-fashion-store.vercel.app`

> **ملاحظة**: كلا المشروعين يتشاركان نفس قاعدة البيانات. الواجهة الأمامية لا تحتاج جداول خاصة بها، لكنها تحتاج الاتصال لجلسات Laravel.

---

### الخطوة 5: تشغيل الترحيلات (Migrations) و Seeders

Vercel ليس لديه SSH/Terminal للوصول إلى lambda. لذا سنشغل الترحيلات بطريقتين:

#### الخيار A: استخدام Vercel CLI (موصى به)

```bash
# ثبّت Vercel CLI
npm i -g vercel

# سجّل الدخول (سيفتح متصفحاً)
vercel login

# ادخل إلى مجلد المشروع الخلفي
cd ecommerce-shop

# نزّل متغيرات البيئة من Vercel إلى ملف .env محلي
vercel env pull .env.production.local --environment=production --yes

# شغّل الترحيلات باستخدام قاعدة البيانات السحابية
php artisan migrate --force --env=production

# شغّل الـ Seeders لإنشاء الأدمن والبيانات الأساسية
php artisan db:seed --force --env=production

# امسح الكاش
php artisan config:clear
php artisan cache:clear
```

#### الخيار B: استخدام أداة خارجية مثل Adminer أو DBeaver

1. نزّل DBeaver: https://dbeaver.io
2. أضف اتصال MySQL جديد:
   - Host: (TiDB Host)
   - Port: 4000
   - Database: elegance_shop
   - User: (TiDB User)
   - Password: (TiDB Password)
   - SSL: مفعّل (Required)
3. انسخ محتوى ملفات الترحيل يدوياً (مُعقّد، لذا الخيار A أفضل)

#### بيانات دخول الأدمن الافتراضية (بعد الـ Seed):
- **البريد**: `admin@elegance.com`
- **كلمة المرور**: `admin123`

---

### الخطوة 6: التحقق من النشر

بعد اكتمال كل شيء، اختبر:

1. **الخلفية** - تحقق من نقطة الصحة:
   ```
   https://elegance-fashion-api.vercel.app/up
   ```
   يجب أن يُرجع: `OK` أو صفحة فارغة بحالة 200

2. **الخلفية** - تحقق من API:
   ```
   https://elegance-fashion-api.vercel.app/api/v1/products
   ```
   يجب أن يُرجع JSON بقائمة المنتجات

3. **الواجهة الأمامية** - افتح المتجر:
   ```
   https://elegance-fashion-store.vercel.app
   ```
   يجب أن تظهر صفحة المتجر مع المنتجات (إذا كانت قاعدة البيانات مُزوّدة)

4. **اختبار الربط**: في صفحة المتجر، افتح Developer Tools → Network، يجب أن ترى طلبات إلى `elegance-fashion-api.vercel.app` ترجع ببيانات.

---

## 🌐 إضافة دومين مخصص (اختياري)

لإضافة دومين مثل `api.elegance-fashion.com` و `shop.elegance-fashion.com`:

### للمشروع الخلفي (elegance-fashion-api):
1. ادخل إلى https://vercel.com/dashboard → اختر `elegance-fashion-api`
2. اذهب إلى **Settings → Domains**
3. أضف: `api.yourdomain.com`
4. اتبع التعليمات لتحديث DNS لدى مُوفّر الدومين:
   - أضف سجل `CNAME` يشير إلى `cname.vercel-dns.com`
   - أو سجل `A` يشير إلى `76.76.21.21`

### للمشروع الأمامي (elegance-fashion-store):
1. اذهب إلى Settings → Domains
2. أضف: `shop.yourdomain.com` أو `yourdomain.com`
3. حدّث DNS بنفس الطريقة

### تحديث API_BASE_URL بعد إضافة الدومين:
إذا أضفت دوميناً مخصصاً للخلفية، حدّث متغير البيئة `API_BASE_URL` في المشروع الأمامي:
1. Vercel Dashboard → `elegance-fashion-store` → Settings → Environment Variables
2. عدّل `API_BASE_URL` ليكون: `https://api.yourdomain.com/api/v1`
3. احفظ، ثم أعد النشر (Redeploy) من تبويب Deployments

---

## 🔧 استكشاف الأخطاء

### المشكلة: خطأ 500 على Vercel
**السبب الأكثر شيوعاً**: متغير `APP_KEY` غير مُعرّف، أو إعدادات DB غير صحيحة.

**الحل**:
1. افتح Vercel Dashboard → Project → **Functions** tab
2. ابحث عن الـ function باسم `api/index.php`
3. اضغط عليها لرؤية الـ Logs
4. ابحث عن رسائل الخطأ المحددة

### المشكلة: `No application encryption key has been specified`
أضف `APP_KEY` في Environment Variables وأعد النشر.

### المشكلة: `SQLSTATE[HY000] [2002] Connection refused`
- تحقق من قيم `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`
- تأكد أن TiDB يسمح بالاتصال من أي IP (في الإعدادات: IP Allow List = `0.0.0.0/0`)
- بالنسبة لـ TiDB Serverless، فعّل TLS: في `config/database.php` أضف خيارات MySQL:
  ```php
  'mysql' => [
      // ... باقي الإعدادات
      'options' => [
          PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
      ],
  ],
  ```

### المشكلة: الواجهة الأمامية لا تعرض المنتجات
1. تأكد أن `API_BASE_URL` مضبوط على `https://elegance-fashion-api.vercel.app/api/v1`
2. افتح Developer Tools → Console، ابحث عن أخطاء CORS
3. جرّب فتح رابط API مباشرة في المتصفح للتحقق من عمله

### المشكلة: الصور لا تظهر
على Vercel، نظام الملفات للقراءة فقط. الصور المرفوعة تُحفظ في `/tmp` وتُفقد عند كل استدعاء. **الحلول**:
- استخدم تخزين سحابي للصور: Cloudinary, AWS S3, أو Vercel Blob
- أو ضع الصور في `public/images/` قبل النشر (ستُخبّأ مع الـ assets)

---

## 📞 بيانات الدخول الافتراضية

- **رابط المتجر**: https://elegance-fashion-store.vercel.app
- **رابط API**: https://elegance-fashion-api.vercel.app/api/v1
- **لوحة الإدارة**: https://elegance-fashion-store.vercel.app/admin
- **البريد**: admin@elegance.com
- **كلمة المرور**: admin123 (غيّرها فور أول دخول!)

---

## 🎯 ملخص البنية النهائية

```
┌─────────────────────────────────────────────────────────────┐
│                       المتصفح (المستخدم)                       │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│        elegance-fashion-store.vercel.app                     │
│        (ecommerce-eshop - Laravel + Blade)                  │
│        - يعرض المنتجات                                       │
│        - يدير السلة والجلسات                                 │
└─────────────────────────┬───────────────────────────────────┘
                          │ HTTP API calls
                          │ API_BASE_URL = https://elegance-fashion-api.vercel.app/api/v1
                          ▼
┌─────────────────────────────────────────────────────────────┐
│        elegance-fashion-api.vercel.app                       │
│        (ecommerce-shop - Laravel + Sanctum)                 │
│        - يقدم /api/v1/products, /categories, /cart, ...     │
│        - يدير لوحة الإدارة                                   │
└─────────────────────────┬───────────────────────────────────┘
                          │ MySQL queries (TLS)
                          ▼
┌─────────────────────────────────────────────────────────────┐
│        TiDB Cloud (MySQL Serverless)                         │
│        - elegance_shop database                              │
│        - Users, Products, Categories, Orders, Cart, ...     │
└─────────────────────────────────────────────────────────────┘
```

تم النشر بنجاح! 🎉
