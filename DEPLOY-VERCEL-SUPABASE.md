# 🚀 دليل النشر الكامل: Vercel + Supabase

دليل عربي خطوة بخطوة لرفع المشروعين (`ecommerce-eshop` + `ecommerce-shop`) على Vercel مع قاعدة بيانات PostgreSQL مجانية من Supabase.

---

## 📋 ما ستحصل عليه

| المشروع | الرابط (مثال) | الوصف |
|---------|---------------|--------|
| `ecommerce-eshop` | `https://elegance-store.vercel.app` | المتجر (الواجهة) |
| `ecommerce-shop` | `https://elegance-api.vercel.app` | API الخلفية |
| Supabase DB | `aws-0-[region].pooler.supabase.com` | قاعدة بيانات PostgreSQL مجانية (500MB) |

**الميزة:** نفس قاعدة البيانات تُستخدم للمشروعين، فالبيانات تبقى متزامنة دائمًا.

---

## 🎯 المتطلبات

- حساب GitHub (مجاني) — [github.com](https://github.com)
- حساب Vercel (مجاني، يُسجّل بـ GitHub) — [vercel.com](https://vercel.com)
- حساب Supabase (مجاني، يُسجّل بـ GitHub) — [supabase.com](https://supabase.com)

---

## 🗂️ الخطوة 1: رفع المشروعين إلى GitHub

### 1.1 إنشاء repo جديد لكل مشروع

1. اذهب إلى GitHub → **New repository**
2. أنشئ repo باسم `elegance-store` (للواجهة)
3. أنشئ repo باسم `elegance-api` (للـ backend)

### 1.2 رفع الكود

على جهازك (إذا عندك Git):

```bash
# رفع الواجهة
cd ecommerce-eshop
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/elegance-store.git
git push -u origin main

# رفع الـ backend
cd ../ecommerce-shop
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/elegance-api.git
git push -u origin main
```

> 💡 **بديل بدون تيرمنال**: ارفع الملفات عبر موقع GitHub مباشرة (New repository → uploading an existing file).

---

## 🗄️ الخطوة 2: إنشاء قاعدة بيانات Supabase

### 2.1 إنشاء مشروع جديد

1. ادخل [supabase.com](https://supabase.com) → **New project**
2. اختر:
   - **Name**: `elegance-db`
   - **Database Password**: اختر كلمة مرور قوية واحفظها! 🔐
   - **Region**: اختر الأقرب (مثلاً `Frankfurt` لأوروبا أو `Singapore` لآسيا)
   - **Plan**: Free
3. انتظر ~2 دقيقة حتى يجهز المشروع

### 2.2 الحصول على بيانات الاتصال

1. ادخل المشروع → **Project Settings** (⚙️) → **Database**
2. في قسم **Connection string** اختر **Transaction** (Pooler) — هذا الموصى به لـ Vercel
3. ستحصل على رابط مثل:
   ```
   postgresql://postgres.[ref]:[YOUR-PASSWORD]@aws-0-[region].pooler.supabase.com:6543/postgres
   ```

> ⚠️ **احفظ هذا الرابط** في مكان آمن — ستحتاجه في الخطوات التالية.

### 2.3 (اختياري) اختبار الاتصال

في **SQL Editor** داخل Supabase جرّب:
```sql
SELECT version();
```
يجب أن يرجع إصدار PostgreSQL (عادة 15.x).

---

## ⚙️ الخطوة 3: توليد APP_KEY

تحتاج مفتاح `APP_KEY` لكل مشروع. الطريقة الأسهل:

1. ادخل [https://laravel-encrypt.vercel.app](https://laravel-encrypt.vercel.app) أو استخدم أي أداة توليد base64
2. أو شغّل الأمر التالي محليًا (إذا عندك PHP):
   ```bash
   php -r "echo 'base64:'.base64_encode(random_bytes(32));"
   ```

> 💡 أو افتح أي موقع base64 generator وولّد سلسلة 44 حرف ثم أضف قبلها `base64:`.

---

## ▲ الخطوة 4: رفع الـ Backend على Vercel

### 4.1 إنشاء المشروع

1. ادخل [vercel.com](https://vercel.com) → **Add New** → **Project**
2. اختر repo `elegance-api`
3. **Framework Preset**: اتركه `Other`
4. **Root Directory**: اتركه `/` (جذر المشروع)
5. **Build Command**: سيُكتشف تلقائيًا من `vercel.json` (`bash vercel-build.sh`)
6. **Output Directory**: سيُكتشف تلقائيًا (`public`)
7. لا تضغط Deploy بعد — أضف Environment Variables أولاً

### 4.2 إضافة Environment Variables

في نفس الصفحة، انزل لـ **Environment Variables** وأضف التالي (نسخ ولصق):

| Key | Value |
|-----|-------|
| `APP_NAME` | `Elegance Fashion API` |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:...` (الذي ولّدته في الخطوة 3) |
| `APP_DEBUG` | `true` |
| `APP_URL` | `https://elegance-api.vercel.app` (ضع رابطك بعد الـ deploy) |
| `APP_LOCALE` | `ar` |
| `APP_FALLBACK_LOCALE` | `en` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | `postgresql://postgres:[PASSWORD]@aws-0-[REGION].pooler.supabase.com:6543/postgres` (من Supabase) |
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

> 💡 يمكنك أيضًا استيرادهم دفعة واحدة عبر ملف `.env.production` الموجود في المشروع.

### 4.3 Deploy

1. اضغط **Deploy**
2. انتظر 3-5 دقائق (البناء أول مرة يأخذ وقت)
3. بعد النجاح ستحصل على رابط مثل: `https://elegance-api.vercel.app`
4. افتح: `https://elegance-api.vercel.app/setup`
   - سيقوم بإنشاء الجداول + بيانات تجريبية + حساب الأدمن
   - يجب أن ترى JSON فيه `success: true`

---

## 🛍️ الخطوة 5: رفع الواجهة على Vercel

### 5.1 إنشاء المشروع

1. في Vercel → **Add New** → **Project**
2. اختر repo `elegance-store`
3. اترك الإعدادات الافتراضية

### 5.2 إضافة Environment Variables

| Key | Value |
|-----|-------|
| `APP_NAME` | `Elegance Fashion Store` |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:...` (مفتاح آخر مختلف عن الـ backend!) |
| `APP_DEBUG` | `true` |
| `APP_URL` | `https://elegance-store.vercel.app` |
| `APP_LOCALE` | `ar` |
| `APP_FALLBACK_LOCALE` | `en` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | `postgresql://postgres:[PASSWORD]@aws-0-[REGION].pooler.supabase.com:6543/postgres` (**نفس رابط Supabase**) |
| `RUN_MIGRATIONS_ON_BOOT` | `true` |
| `API_BASE_URL` | `https://elegance-api.vercel.app/api/v1` (رابط الـ backend من الخطوة 4) |
| `API_TIMEOUT` | `15` |
| `API_RETRIES` | `2` |
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

### 5.3 Deploy

1. اضغط **Deploy**
2. افتح الرابط النهائي: `https://elegance-store.vercel.app`
3. يجب أن تظهر الصفحة الرئيسية للمتجر بالعربية
4. جرّب الدخول بحساب الأدمن:
   - **البريد**: `admin@elegance.com`
   - **كلمة المرور**: `admin123`

---

## ✅ الخطوة 6: التحقق من العمل

### 6.1 اختبار الـ Backend API

افتح في المتصفح:
```
https://elegance-api.vercel.app/api/v1/products
```
يجب أن ترى JSON فيه قائمة المنتجات.

### 6.2 اختبار الواجهة

1. افتح `https://elegance-store.vercel.app`
2. تأكد أن المنتجات تظهر (هذا يعني الاتصال بقاعدة البيانات ناجح)
3. جرّب إضافة منتج للسلة
4. جرّب تسجيل الدخول كأدمن

### 6.3 فحص الـ Migration Log

إذا واجهت مشاكل في قاعدة البيانات:
```
https://elegance-store.vercel.app/_debug/migrate
https://elegance-api.vercel.app/_debug/migrate
```
يعرض سجل المايغريشن وإصدار PostgreSQL.

---

## 🔧 استكشاف الأخطاء وإصلاحها

### المشكلة: `500 Server Error` بعد الـ deploy

**السبب المحتمل**: `APP_KEY` غير مضبوط أو غير صحيح.

**الحل**:
1. تأكد أن `APP_KEY` يبدأ بـ `base64:` ويليه 44 حرف
2. أعد الـ deploy بعد إضافة المتغير

---

### المشكلة: `SQLSTATE[08006]` أو خطأ اتصال بقاعدة البيانات

**السبب**: بيانات Supabase غير صحيحة.

**الحل**:
1. تأكد أن `DB_URL` بالصيغة الصحيحة:
   ```
   postgresql://postgres:[PASSWORD]@aws-0-[REGION].pooler.supabase.com:6543/postgres
   ```
2. استبدل `[PASSWORD]` بكلمة مرور قاعدة البيانات (وليس كلمة مرور حساب Supabase)
3. جرّب الاتصال عبر المنفذ `5432` بدلاً من `6543` (Direct connection بدلاً من Pooler)

---

### المشكلة: المنتجات لا تظهر في الواجهة

**السبب المحتمل**: لم يتم تشغيل `/setup`.

**الحل**:
1. افتح: `https://elegance-api.vercel.app/setup` (هذا يزرع البيانات)
2. أو افتح: `https://elegance-store.vercel.app/setup` (نفس البيانات ستُزرع)

> 💡 لا بأس بتشغيل `/setup` عدة مرات — كل أمر يستخدم `updateOrCreate` فلا يكرر البيانات.

---

### المشكلة: الـ backend يعمل لكن الواجهة لا تتصل

**السبب**: `API_BASE_URL` غير مضبوط في الواجهة.

**الحل**:
1. في Vercel → مشروع الواجهة → Settings → Environment Variables
2. أضف: `API_BASE_URL = https://elegance-api.vercel.app/api/v1`
3. أعد الـ deploy (Redeploy من Deployments)

---

### المشكلة: `vercel-php` runtime not found

**السبب**: Vercel لا يدعم PHP افتراضيًا.

**الحل**: تأكد أن `vercel.json` يحتوي على:
```json
"functions": {
  "api/index.php": {
    "runtime": "vercel-php@0.7.4"
  }
}
```

---

## 💡 نصائح مهمة

### 1. الـ Cold Start بطيء أول مرة
Vercel Serverless "ينام" بعد فترة من عدم النشاط. أول زيارة بعد النوم تأخذ ~3-5 ثوان. لتسريع الاستجابة:
- استخدم Vercel Cron Jobs لضبط الـ ping كل 5 دقائق
- أو رقِّ إلى Pro plan (مدفوع)

### 2. الـ Supabase Free Tier
- 500MB تخزين
- مشروع واحد يكفي للمشروعين
- ينام بعد أسبوع من عدم النشاط (يكفي أي زيارة لإيقاظه)

### 3. تعديل الكود
عند تعديل الكود محليًا:
1. `git push origin main`
2. Vercel سيعيد الـ deploy تلقائيًا
3. لا حاجة لأي أوامر تيرمنال على السيرفر

### 4. النسخ الاحتياطي
- Supabase يأخذ نسخ احتياطية يومية (في الـ Pro plan)
- في الـ Free plan: صدّر قاعدة البيانات يدويًا من SQL Editor:
  ```sql
  COPY (SELECT * FROM products) TO '/tmp/products.csv' WITH CSV HEADER;
  ```

---

## 📞 الدعم

إذا واجهت مشكلة:
1. افتح `https://[your-store].vercel.app/_debug/migrate` لرؤية سجل الأخطاء
2. افتح Vercel Dashboard → Project → Logs لرؤية الـ runtime logs
3. افتح Supabase → SQL Editor لتشخيص مشاكل قاعدة البيانات

---

## 🎉 مبروك!

إذا وصلت لهذه النقطة، فمتجرك يعمل بالكامل على:
- ✅ Frontend على Vercel (مجاني)
- ✅ Backend API على Vercel (مجاني)
- ✅ PostgreSQL على Supabase (مجاني للأبد، 500MB)
- ✅ HTTPS تلقائي
- ✅ CDN عالمي
- ✅ Deploy تلقائي عند كل `git push`

**التكلفة الإجمالية: 0$ شهريًا** 🎊
