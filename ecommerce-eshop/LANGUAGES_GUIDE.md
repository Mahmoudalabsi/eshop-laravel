# إدارة اللغات - دليل الاستخدام

## نظرة عامة
تم إنشاء نظام إدارة اللغات في الداشبورد بنفس طريقة إدارة العملات، حيث يمكنك:
- ✅ إضافة لغات جديدة
- ✅ تعديل اللغات الموجودة
- ✅ تفعيل/تعطيل اللغات (زر التبديل)
- ✅ حذف اللغات (باستثناء اللغة الافتراضية)
- ✅ تعيين لغة افتراضية للمتجر

## الملفات المنشأة

### 1. Models
- **[app/Models/Language.php](../../app/Models/Language.php)** - نموذج اللغة

### 2. Services
- **[app/Services/LanguageService.php](../../app/Services/LanguageService.php)** - خدمة اللغات التي تتواصل مع API

### 3. Controllers
- **[app/Http/Controllers/LanguageController.php](../../app/Http/Controllers/LanguageController.php)** - متحكم اللغات

### 4. Views
- **[resources/views/admin/languages/index.blade.php](../../resources/views/admin/languages/index.blade.php)** - صفحة إدارة اللغات
- **[resources/views/admin/languages/script.blade.php](../../resources/views/admin/languages/script.blade.php)** - كود JavaScript لإدارة اللغات

### 5. Routes
تم إضافة المسارات التالية في [routes/web.php](../../routes/web.php):
```php
Route::prefix('admin')->name('admin.')->group(function () {
    // Languages Management
    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('/languages-json', [LanguageController::class, 'getLanguagesJson'])->name('languages.json');
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::put('/languages/{id}', [LanguageController::class, 'update'])->name('languages.update');
    Route::post('/languages/{id}/status', [LanguageController::class, 'updateStatus'])->name('languages.status');
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');
});

// Language switcher
Route::get('/set-language/{code}', [LanguageController::class, 'setLanguage'])->name('language.set');
```

## كيفية الوصول
1. اذهب إلى `/admin/languages` للوصول إلى صفحة إدارة اللغات
2. يجب أن تكون مسجل دخول (authenticated) للوصول لإدارة اللغات

## الميزات

### إضافة لغة جديدة
- اسم اللغة (مثلاً: العربية)
- كود اللغة (حرفين، مثلاً: AR، EN)
- العلم (Emoji، مثلاً: 🇸🇦)
- الاتجاه: RTL (يمين لليسار) أو LTR (يسار لليمين)
- تحديد ما إذا كانت اللغة افتراضية

### تفعيل/تعطيل اللغات
كل لغة لها زر تبديل لتفعيلها أو تعطيلها مباشرة من الجدول

### حذف اللغات
يمكنك حذف أي لغة (ما عدا الافتراضية) مباشرة من الجدول

### تعيين اللغة الحالية
يمكنك تعيين اللغة الحالية للجلسة عبر:
```php
// في الكود
\Illuminate\Support\Facades\Session::put('language', 'ar');

// من الواجهة
// زر اختيار اللغة يستدعي: /set-language/{code}
```

## تكامل مع API

### طرق اللغات المتاحة:
```
GET  /api/v1/languages              - جلب جميع اللغات
GET  /api/v1/languages/{id}         - جلب لغة معينة
POST /api/v1/languages              - إضافة لغة جديدة
POST /api/v1/languages/{id}         - تحديث لغة
POST /api/v1/languages/{id}/status  - تحديث حالة اللغة
POST /api/v1/languages/{id}/delete  - حذف لغة
```

## مثال الاستخدام في الواجهة الأمامية

```blade
<!-- عرض اختيار اللغة -->
<div class="language-selector">
    @foreach ($languages as $language)
        <a href="{{ route('language.set', $language->code) }}" 
           class="btn btn-sm {{ $language->code == session('language') ? 'active' : '' }}">
            {{ $language->flag ?? '🌐' }} {{ $language->name }}
        </a>
    @endforeach
</div>
```

## الأخطاء الشائعة والحلول

### لا يمكن إضافة اللغة
- تأكد من أن API متصل وأن النقطة الطرفية `/languages` موجودة
- تحقق من توكن المصادقة في الجلسة

### لا يمكن حذف اللغة الافتراضية
- هذا السلوك مقصود - يجب أن تكون هناك لغة واحدة على الأقل افتراضية

### المسارات لا تعمل
- تأكد من أن الـ routes مسجلة في `routes/web.php`
- أعد حذف cache الـ routes بـ: `php artisan route:clear`

## تحديثات مستقبلية
يمكن إضافة:
- معاينة حية للاتجاه (RTL/LTR)
- ترجمة تلقائية للمحتوى
- إدارة المترجمات والترجمات
- إحصائيات استخدام اللغات
