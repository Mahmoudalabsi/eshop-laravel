# 🚀 دليل النشر: Elegance Fashion على Vercel + Supabase

> **الحالة**: ✅ قاعدة بيانات Supabase مُنشأة بالكامل ومُهيّأة بالبيانات الأولية  
> **المشروع**: `elegance-prod` (Supabase, eu-central-1)  
> **المتبقي**: نشر المشروعين على Vercel فقط

---

## ✅ ما تم إنجازه (لا تحتاج تكراره)

| المكون | الحالة |
|---|---|
| مشروع Supabase `elegance-prod` | ✅ مُنشأ (ref: `ofehrwapsjlfoersvxet`) |
| قاعدة البيانات PostgreSQL 17 | ✅ تعمل على `aws-1-eu-central-1.pooler.supabase.com:6543` |
| جميع الجداول (24 جدول) | ✅ مُنشأة (users, products, orders, cart_items, ...) |
| البيانات الأولية | ✅ مُدخلة (admin, 8 عملات، لغتان، 4 أقسام، 8 منتجات) |
| ملفات `.env.production` للمشروعين | ✅ مُحدّثة بالقيم الصحيحة |
| سكربت النشر `scripts/deploy-vercel-supabase.sh` | ✅ مُحدّث |

---

## 🔑 بيانات الاعتماد الجاهزة للاستخدام

### قاعدة البيانات (Supabase)
```
DB_CONNECTION=pgsql
DB_URL=postgresql://postgres.ofehrwapsjlfoersvxet:EleganceShop2026%21Secure@aws-1-eu-central-1.pooler.supabase.com:6543/postgres
```

### مفاتيح Laravel APP_KEY (مولّدة مسبقاً)
- **Frontend (ecommerce-eshop)**: `base64:BOdLkv68u7lm8ZDC4QyuRUdB7q5qYtyBy/qc934NHrM=`
- **Backend (ecommerce-shop)**: `base64:GpG7ZNM/+Kx7Tq+0KpDhDdw/CohOY9hHJXtwyg9UI04=`

### حساب الأدمن
- **البريد**: `admin@elegance.com`
- **كلمة المرور**: `admin123`

---

## 🚀 طريقة النشر 1: سكربت آلي (يتطلب Vercel CLI)

إذا كان عندك جهاز به Vercel CLI مُثبّت:

```bash
# استنسخ المستودع
git clone https://github.com/Mahmoudalabsi/eshop-laravel.git
cd eshop-laravel

# شغّل سكربت النشر
chmod +x scripts/deploy-vercel-supabase.sh
./scripts/deploy-vercel-supabase.sh
```

السكربت سيقوم بـ:
1. نشر الـ Backend على `elegance-api.vercel.app`
2. زرع البيانات الأولية (تلقائياً عبر `/setup`)
3. نشر الواجهة على `elegance-store.vercel.app`

---

## 🚀 طريقة النشر 2: Vercel Dashboard (بدون أي تيرمنال)

### الخطوة 1: ارفع الكود لـ GitHub
الكود موجود مسبقاً في: https://github.com/Mahmoudalabsi/eshop-laravel

### الخطوة 2: اربط Vercel بـ GitHub
1. ادخل [vercel.com](https://vercel.com) → **Sign Up / Log In** (بـ GitHub)
2. اضغط **Add New Project**
3. اختر مستودع `Mahmoudalabsi/eshop-laravel`
4. **مهم**: في **Root Directory** اختر `ecommerce-shop` (للـ API أولاً)

### الخطوة 3: متغيرات البيئة للـ Backend (ecommerce-shop)
في صفحة الإعداد، أضف هذه المتغيرات في **Environment Variables**:

| Variable | Value |
|---|---|
| `APP_NAME` | `Elegance Fashion API` |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:GpG7ZNM/+Kx7Tq+0KpDhDdw/CohOY9hHJXtwyg9UI04=` |
| `APP_DEBUG` | `false` |
| `APP_LOCALE` | `ar` |
| `APP_FALLBACK_LOCALE` | `en` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | `postgresql://postgres.ofehrwapsjlfoersvxet:EleganceShop2026%21Secure@aws-1-eu-central-1.pooler.supabase.com:6543/postgres` |
| `RUN_MIGRATIONS_ON_BOOT` | `true` |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `local` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `warning` |
| `SANCTUM_STATEFUL_DOMAINS` | `*` |
| `VIEW_COMPILED_PATH` | `/tmp/storage/framework/views` |
| `APP_CONFIG_CACHE` | `/tmp/config.php` |
| `APP_EVENTS_CACHE` | `/tmp/events.php` |
| `APP_ROUTES_CACHE` | `/tmp/routes.php` |

اضغط **Deploy** وانتظر 3-5 دقائق. ستتحصل على رابط مثل:
`https://elegance-api-xxx.vercel.app`

### الخطوة 4: نشر الواجهة (ecommerce-eshop)
كرّر نفس العملية لكن:
- **Root Directory**: `ecommerce-eshop`
- **Project Name**: `elegance-store`

نفس متغيرات البيئة، مع تغيير:
- `APP_NAME` = `Elegance Fashion Store`
- `APP_KEY` = `base64:BOdLkv68u7lm8ZDC4QyuRUdB7q5qYtyBy/qc934NHrM=`
- `API_BASE_URL` = `https://elegance-api-xxx.vercel.app/api/v1` (رابط الـ API من الخطوة 3)

---

## 🚀 طريقة النشر 3: Vercel API (تلقائي بالكامل، يتطلب Vercel Token)

### 1. أنشئ Vercel API Token
1. ادخل [vercel.com → Account Settings → Tokens](https://vercel.com/account/tokens)
2. اضغط **Create Token** → سَمِّه `deployment-script`
3. انسخ التوكن (يبدأ بـ `vercel_...`)

### 2. شغّل سكربت النشر عبر API
```bash
export VERCEL_TOKEN="vercel_xxxxxxxxxxxxxxxxxxxxxx"
chmod +x scripts/deploy-vercel-api.sh
./scripts/deploy-vercel-api.sh
```

السكربت سينشر المشروعين تلقائياً بدون أي تفاعل.

---

## ✅ التحقق من النشر

بعد اكتمال النشر:

1. **افتح الواجهة**: `https://elegance-store.vercel.app`
2. **سجّل دخول كأدمن**:
   - البريد: `admin@elegance.com`
   - كلمة المرور: `admin123`
3. **تحقق من API**: `https://elegance-api.vercel.app/api/v1/products`
4. **لوحة تحكم Supabase**: https://supabase.com/dashboard/project/ofehrwapsjlfoersvxet

---

## 🛠️ استكشاف الأخطاء

### المشكلة: صفحة بيضاء عند فتح الموقع
**السبب**: غالباً لم تُضبط `APP_KEY` أو `DB_URL` بشكل صحيح في Vercel env vars.  
**الحل**: تحقق من Vercel → Project → Settings → Environment Variables.

### المشكلة: خطأ `SQLSTATE[08006]` عند الاتصال بقاعدة البيانات
**السبب**: كلمة المرور في `DB_URL` غير مُرمّزة.  
**الحل**: استبدل `!` بـ `%21` في كلمة المرور (موجود في القيم أعلاه).

### المشكلة: لم تُزرع البيانات الأولية
**السبب**: الـ `/setup` endpoint لم يُستدعَ.  
**الحل**: افتح في المتصفح: `https://elegance-store.vercel.app/setup` (أو `https://elegance-api.vercel.app/setup`)

### المشكلة: المايغريشن لا يعمل
**الحل**: افتح `https://elegance-store.vercel.app/_debug/migrate` لرؤية سجل المايغريشن.

---

## 📞 الدعم

- **Supabase Dashboard**: https://supabase.com/dashboard/project/ofehrwapsjlfoersvxet
- **Vercel Dashboard**: https://vercel.com/dashboard
- **GitHub Repo**: https://github.com/Mahmoudalabsi/eshop-laravel

---

## 📋 ملخص البنية النهائية

```
┌─────────────────────────────────────────────┐
│  المستخدم (المتصفح)                          │
└────────────────┬────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────┐
│  Vercel: elegance-store.vercel.app          │
│  (ecommerce-eshop / Laravel frontend)       │
│  PHP 8.3 serverless                         │
└────────────────┬────────────────────────────┘
                 │ HTTP API calls
                 ▼
┌─────────────────────────────────────────────┐
│  Vercel: elegance-api.vercel.app            │
│  (ecommerce-shop / Laravel backend API)     │
│  PHP 8.3 serverless + Sanctum              │
└────────────────┬────────────────────────────┘
                 │ PDO pgsql (port 6543, SSL)
                 ▼
┌─────────────────────────────────────────────┐
│  Supabase: elegance-prod                    │
│  PostgreSQL 17 + Pooler (Supavisor)         │
│  Region: eu-central-1 (Frankfurt)           │
│  500MB free tier                            │
└─────────────────────────────────────────────┘
```
