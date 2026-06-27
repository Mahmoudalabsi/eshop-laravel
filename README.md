# 🛍️ Elegance Fashion — Laravel 12 E-commerce

متجر إلكتروني عربي (عربي/إنجليزي) مبني على Laravel 12 + Blade + TailwindCSS.

## 📦 بنية المشروع

```
eshop-laravel/
├── ecommerce-shop/      # Backend: لوحة التحكم + REST API (يستخدم Sanctum)
└── ecommerce-eshop/     # Frontend: واجهة المتجر (يستهلك API من الأول)
```

| التطبيق | المنفذ (محلياً) | الوصف |
|---------|-----------------|-------|
| `ecommerce-shop` | 8000 | Admin Dashboard + API ( Sanctum ) |
| `ecommerce-eshop` | 8001 | واجهة المتجر للزبائن |

كلاهما يتشاركان قاعدة بيانات MySQL واحدة (`ecomerce_shop`).

## 🚀 التشغيل محلياً

### المتطلبات
- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.4+
- Composer 2
- Node.js 18+ و npm

### خطوات التشغيل

```bash
# 1) جهّز قاعدة بيانات MySQL باسم ecomerce_shop

# 2) جهّز التطبيقين (نفّذ هذا في كل من ecommerce-shop/ و ecommerce-eshop/)
cd ecommerce-shop
cp .env.example .env
# عدّل بيانات DB في .env (DB_HOST, DB_USERNAME, DB_PASSWORD)
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build

cd ../ecommerce-eshop
cp .env.example .env
# عدّل API_BASE_URL ليشير إلى http://127.0.0.1:8000/api/v1
composer install
php artisan key:generate
npm install && npm run build

# 3) شغّل الـ Backend ثم الـ Frontend في تيرمينالين منفصلين
cd ecommerce-shop   && php artisan serve --port=8000
cd ecommerce-eshop  && php artisan serve --port=8001
```

افتح المتصفح على:
- المتجر: http://127.0.0.1:8001
- لوحة التحكم: http://127.0.0.1:8000

---

## ☁️ النشر على Vercel

> ⚠️ **مهم**: Vercel لا يدعم Laravel/PHP رسمياً، لكننا نستخدم runtime مجتمعي `vercel-php` الذي يعمل بشكل جيد للواجهات البسيطة. لكل تطبيق سيتم إنشاء Vercel Project منفصل.

### القيود الحالية على Vercel
1. ❌ لا يمكن تشغيل `php artisan migrate` أثناء النشر (serverless) — يجب تشغيل migrations يدوياً ضد MySQL السحابي.
2. ❌ لا يوجد storage دائم — يجب استخدام S3 لتخزين صور المنتجات.
3. ⚠️ لا توجد عمليات طويلة (queues/workers) — يُنصح بنقل الـ queue إلى Render/Railway لاحقاً.
4. ⚠️ كل استدعاء دالة serverless — زمن استجابة أعلى قليلاً من خادم دائم.

### خطوات النشر

#### 1) أنشئ قاعدة بيانات MySQL سحابية مجانية

اختر إحدى الخيارات:
- **Aiven** (موصى — 5GB مجاني): https://aiven.io
- **TiDB Cloud** (Serverless — 5GB مجاني): https://tidbcloud.com
- **Clever Cloud** (10MB فقط — للحجم الكبير اشترك مدفوع): https://clever-cloud.com

احفظ: `host`, `port`, `database name`, `username`, `password`.

#### 2) اربط المستودع بـ Vercel

1. ادخل https://vercel.com/new
2. اختر مستودع `Mahmoudalabsi/eshop-laravel`
3. **Root Directory** = `ecommerce-shop` (للـ Backend)
4. Framework Preset = "Other"
5. Build Command = `npm install && npm run build` (موجود في vercel.json)
6. Output Directory = `public` (موجود في vercel.json)

#### 3) أضف متغيرات البيئة في Vercel

في صفحة المشروع → Settings → Environment Variables، أضف:

| Variable | Value |
|----------|-------|
| `APP_NAME` | `Elegance Fashion API` |
| `APP_ENV` | `production` |
| `APP_KEY` | (نفّذ `php artisan key:generate` محلياً وانسخ القيمة) |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-project.vercel.app` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | (من مزود MySQL السحابي) |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | (اسم القاعدة) |
| `DB_USERNAME` | (المستخدم) |
| `DB_PASSWORD` | (كلمة المرور) |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_CHANNEL` | `stderr` |

#### 4) كرّر نفس الخطوات لـ `ecommerce-eshop`

- Root Directory = `ecommerce-eshop`
- نفس متغيرات البيئة، لكن أضف أيضاً:
  - `API_BASE_URL` = `https://your-shop-backend.vercel.app/api/v1`
  - `APP_NAME` = `Elegance Fashion`

#### 5) شغّل migrations يدوياً

من جهازك المحلي بعد ضبط `.env` على بيانات MySQL السحابي:

```bash
cd ecommerce-shop
php artisan migrate --force
php artisan db:seed --force
```

#### 6) اختبر الروابط

- المتجر: `https://ecommerce-eshop-xxx.vercel.app`
- API: `https://ecommerce-shop-xxx.vercel.app/api/v1/products`

---

## 🆘 بديل أسهل: Render.com

إذا واجهت مشاكل مع Vercel، Render يدعم Laravel أصلياً عبر Buildpack:

1. https://render.com → New → Web Service → اربط نفس المستودع
2. Root = `ecommerce-shop` أو `ecommerce-eshop`
3. Environment = PHP
4. Build = `composer install && npm install && npm run build`
5. Start = `php artisan serve --host 0.0.0.0 --port $PORT`
6. أضف MySQL addon داخلي (Free tier: 90 يوم)

انظر `DEPLOY.md` للتفاصيل الكاملة.

---

## 📚 ملفات مهمة

| الملف | الوصف |
|------|-------|
| `ecommerce-shop/.env.production.example` | قالب الإعداد للإنتاج (Backend) |
| `ecommerce-eshop/.env.production.example` | قالب الإعداد للإنتاج (Frontend) |
| `ecommerce-shop/vercel.json` | إعدادات النشر على Vercel (Backend) |
| `ecommerce-eshop/vercel.json` | إعدادات النشر على Vercel (Frontend) |
| `DEPLOY.md` | دليل نشر كامل بالعربية (Vercel + Render + Railway) |
| `ecommerce-shop/QUICK_START.md` | دليل التشغيل المحلي السريع |
| `ecommerce-shop/DOCUMENTATION.md` | توثيق المشروع |

## 🔐 الأمان

- لا ترفع ملف `.env` إلى Git أبداً (موجود في `.gitignore`).
- استخدم `APP_KEY` مختلفاً لكل بيئة.
- في الإنتاج: `APP_DEBUG=false` دائماً.
- بعد أول نشر، أنشئ مستخدم admin عبر `php artisan tinker` ثم احذف صلاحيات `admin@local`.

## 📜 الترخيص

MIT License — انظر LICENSE في كل تطبيق.
