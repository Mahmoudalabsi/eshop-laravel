# دليل النشر: Vercel + Neon PostgreSQL

> **الهدف**: نشر متجر Elegance Fashion على Vercel (مشروعان منفصلان) باستخدام قاعدة بيانات Neon PostgreSQL مجانية.

---

## ⏱️ الوقت المتوقع: 15 دقيقة

## 📋 ما ستحصل عليه في النهاية:
- **API الخلفية**: `https://elegance-fashion-api.vercel.app`
- **متجر الواجهة**: `https://elegance-fashion-store.vercel.app`
- **قاعدة بيانات**: Neon PostgreSQL (مجانية للأبد، 0.5GB)

---

## 🗄️ الخطوة 1: إنشاء قاعدة بيانات Neon (3 دقائق)

1. اذهب إلى: **https://neon.tech/signup**
2. سجّل بحساب GitHub (نفس حسابك `Mahmoudalabsi`)
3. اضغط **"Create Project"** وأدخل:
   - **Project name**: `elegance-fashion`
   - **Postgres version**: 16
   - **Region**: `AWS EU North (Stockholm)` — الأقرب لخوادم Vercel
4. اضغط **"Create Project"**
5. في صفحة **"Connection Details"**:
   - بدّل إلى **"Pooled connection"** (مهم للأداء)
   - اختر **Database**: `neondb`
   - اختر **Role**: `neondb_owner`
6. **انسخ هذه البيانات** (ستحتاجها في الخطوة 3):
   ```
   Host:     ep-xxxxx-xxxx-pooler.eu-north-1.aws.neon.tech
   Database: neondb
   User:     neondb_owner
   Password: xxxxxxxxxxxxxxxx
   ```

---

## 🔑 الخطوة 2: توليد APP_KEY (30 ثانية)

افتح Terminal/CMD والصق هذا الأمر:

```bash
node -e "console.log('base64:' + Buffer.from(crypto.randomBytes(32)).toString('base64'))"
```

ستحصل على شيء مثل:
```
base64:W4YZkqSXcK7F2qN3vJ8Xy5b1pTqj6xKQn8vLz9yW0aM=
```

انسخه (ستحتاجه في الخطوة 3).

---

## 🚀 الخطوة 3: نشر المشروع الخلفي على Vercel (5 دقائق)

1. اذهب إلى: **https://vercel.com/new**
2. سجّل الدخول بحساب **GitHub** (نفس حسابك `Mahmoudalabsi`)
3. اختر مستودع: **eshop-laravel**
4. في صفحة الإعدادات:
   - **Project Name**: `elegance-fashion-api`
   - **Framework Preset**: اتركه Other
   - **Root Directory**: اضغط Edit → اختر `ecommerce-shop` → Confirm
5. افتح قسم **"Environment Variables"** وأضف هذه المتغيرات (انسخها من `VERCEL-ENV.txt`):

| Key | Value |
|-----|-------|
| `APP_KEY` | (الـ APP_KEY الذي ولّدته في الخطوة 2) |
| `APP_URL` | `https://elegance-fashion-api.vercel.app` |
| `APP_NAME` | `Elegance Fashion API` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_LOCALE` | `ar` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | (Neon Host من الخطوة 1) |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `neondb` |
| `DB_USERNAME` | `neondb_owner` |
| `DB_PASSWORD` | (Neon Password من الخطوة 1) |
| `DB_SSLMODE` | `require` |
| `DB_SSLROOTCERT` | `/etc/ssl/certs/ca-certificates.crt` |
| `SANCTUM_STATEFUL_DOMAINS` | `*` |
| `RUN_MIGRATIONS_ON_BOOT` | `true` |

6. اضغط **"Deploy"** وانتظر 3-5 دقائق
7. ستظهر لك رسالة: **"Congratulations!"** مع الرابط:
   👉 `https://elegance-fashion-api.vercel.app`

---

## 🛍️ الخطوة 4: نشر المشروع الأمامي على Vercel (5 دقائق)

1. اذهب مرة أخرى إلى: **https://vercel.com/new**
2. اختر نفس المستودع: **eshop-laravel**
3. في صفحة الإعدادات:
   - **Project Name**: `elegance-fashion-store`
   - **Framework Preset**: Other
   - **Root Directory**: اضغط Edit → اختر `ecommerce-eshop` → Confirm
4. أضف متغيرات البيئة:

| Key | Value |
|-----|-------|
| `APP_KEY` | (نفس APP_KEY من الخطوة 2) |
| `APP_URL` | `https://elegance-fashion-store.vercel.app` |
| `APP_NAME` | `Elegance Fashion Store` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_LOCALE` | `ar` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | (نفس Neon Host) |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `neondb` |
| `DB_USERNAME` | `neondb_owner` |
| `DB_PASSWORD` | (نفس Neon Password) |
| `DB_SSLMODE` | `require` |
| `DB_SSLROOTCERT` | `/etc/ssl/certs/ca-certificates.crt` |
| `API_BASE_URL` | `https://elegance-fashion-api.vercel.app/api/v1` |
| `API_TIMEOUT` | `15` |
| `API_RETRIES` | `2` |
| `SANCTUM_STATEFUL_DOMAINS` | `*` |

5. اضغط **"Deploy"** وانتظر 3-5 دقائق
6. ستحصل على: 👉 `https://elegance-fashion-store.vercel.app`

---

## ✅ الخطوة 5: التحقق (دقيقتان)

الآن المigrations والـ seeders تعمل **تلقائياً** عند أول زيارة (بفضل `RUN_MIGRATIONS_ON_BOOT=true`).

### اختبر الـ API:
افتح في المتصفح:
```
https://elegance-fashion-api.vercel.app/up
```
يجب أن يُرجع: `OK`

### اختبر المنتجات:
```
https://elegance-fashion-api.vercel.app/api/v1/products
```
يجب أن يُرجع JSON بقائمة المنتجات (الـ seeder أنشأها تلقائياً).

### اختبر المتجر:
```
https://elegance-fashion-store.vercel.app
```
يجب أن تظهر صفحة المتجر مع المنتجات.

### بيانات دخول الأدمن:
- **البريد**: `admin@elegance.com`
- **كلمة المرور**: `admin123`
- **لوحة الإدارة**: `https://elegance-fashion-store.vercel.app/admin`

---

## 🔧 استكشاف الأخطاء

### المشكلة: خطأ 500 على `/up`
**السبب**: غالباً مشكلة في اتصال قاعدة البيانات.

**الحل**:
1. افتح Vercel Dashboard → `elegance-fashion-api` → **Functions**
2. اضغط على `api/index.php` لرؤية الـ Logs
3. ابحث عن رسائل مثل:
   - `SQLSTATE[08006]` → اتصال SSL مرفوض (تأكد أن `DB_SSLMODE=require`)
   - `password authentication failed` → خطأ في `DB_PASSWORD`
   - `database "neondb" does not exist` → خطأ في `DB_DATABASE`

### المشكلة: المتجر لا يعرض المنتجات
1. تأكد أن `API_BASE_URL` مضبوط على `https://elegance-fashion-api.vercel.app/api/v1`
2. افتح Developer Tools → Console → ابحث عن أخطاء CORS
3. جرّب فتح رابط API مباشرة في المتصفح للتحقق

### المشكلة: migrations لم تعمل
- الكود يحفظ "marker file" في `/tmp/storage/migrations_complete.txt` بعد أول تشغيل ناجح
- إذا فشلت المigrations، لن يُكتب الـ marker وستحاول مرة أخرى في الطلب التالي
- لإعادة التشغيل: اذهب إلى Vercel Dashboard → **Redeploy** (سيُمحى `/tmp` ويعاد البناء)

### المشكلة: `pdo_pgsql` extension missing
- هذا غير متوقع لأن `vercel-php@0.7.4` يتضمن pdo_pgsql
- إذا حدث: حدّث `vercel.json` إلى `vercel-php@0.8.x` أو `vercel-php@0.9.x`

---

## 📞 الروابط النهائية

| الخدمة | الرابط |
|--------|--------|
| **API (الخلفية)** | https://elegance-fashion-api.vercel.app |
| **API Docs** | https://elegance-fashion-api.vercel.app/api/v1/products |
| **Health Check** | https://elegance-fashion-api.vercel.app/up |
| **المتجر (الواجهة)** | https://elegance-fashion-store.vercel.app |
| **لوحة الإدارة** | https://elegance-fashion-store.vercel.app/admin |
| **Neon Console** | https://console.neon.tech |
| **Vercel Dashboard** | https://vercel.com/dashboard |

---

## 🎯 ملاحظات مهمة

1. **الـ Migrations تعمل تلقائياً** عند أول زيارة بفضل `RUN_MIGRATIONS_ON_BOOT=true`. لا حاجة لأي أوامر يدوية.

2. **الـ Cold Start بطيء قليلاً** (2-3 ثوانٍ لأول طلب بعد خمول) — هذا طبيعي في Vercel.

3. **بياناتك محفوظة في Neon** (ليست في `/tmp`) — لن تُفقد عند إعادة النشر.

4. **الـ Seeders تعمل مرة واحدة** — يتحقق الكود من جدول `users` قبل التشغيل، فلا تكرار.

5. **الحد المجاني لـ Neon**: 0.5GB تخزين + 100 ساعة نشطة شهرياً — كافٍ لمتجر صغير.

تم النشر بنجاح! 🎉
