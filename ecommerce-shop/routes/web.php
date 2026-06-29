<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\LanguageController;
use Illuminate\Support\Facades\Route;

// Vercel-safe data initialization (no artisan, no seeder)
Route::get('/setup', [\App\Http\Controllers\SetupController::class, 'index'])->name('setup');

// مسار مؤقت لإعادة تعيين كلمة المرور (آمن: مفعّل فقط في بيئة local)
if (app()->environment('local')) {
    Route::get('/reset-pass-force', function() {
        $user = \App\Models\User::where('email', 'admin@elegance.com')->first() ?: \App\Models\User::first();
        if($user) {
            $user->password = \Illuminate\Support\Facades\Hash::make('admin123');
            $user->save();
            return "تم إعادة تعيين كلمة المرور بنجاح للمستخدم: " . $user->email . " الكلمة الجديدة هي: admin123 <br> <a href='/login'>اذهب لتسجيل الدخول</a>";
        }
        return "لم يتم العثور على أي مستخدم في قاعدة البيانات.";
    });
}

// المسار الرئيسي وتوجيه لوحة التحكم
Route::get('/', function () {
    return auth()->check() ? view('welcome') : redirect()->route('login');
})->name('home');

Route::get('/dashboard', fn() => redirect()->route('home'))->middleware(['auth'])->name('dashboard');

// Language selection route
Route::get('/set-language/{code}', [LanguageController::class, 'setLanguage'])->name('language.set');

// --- مسارات الـ Admin ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // 1. Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard-sales', [DashboardController::class, 'getSalesData'])->name('admin.dashboard.sales');

    // 2. Users
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users-json', 'getUsersJson');
        Route::post('/users-store', 'store')->name('users.store');
        Route::delete('/users-delete/{id}', 'destroy');
        Route::patch('/users/update-role/{id}', 'updateRole')->name('users.updateRole');
        Route::patch('/users/update-status/{id}', 'updateStatus');
    });

    // 3. Categories (الأقسام الرئيسية)
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index')->name('categories.index');
        Route::get('/categories-json', 'getCategoriesJson')->name('categories.json');
        Route::post('/categories-store', 'store')->name('categories.store');
        Route::post('/categories-update/{id}', 'update')->name('categories.update');
        Route::delete('/categories-delete/{id}', 'destroy')->name('categories.destroy');
        Route::patch('/categories/update-status/{id}', 'updateStatus');
        Route::get('/categories/{id}/subcategories', 'getSubcategoriesForCategory');
    });

    // 4. Subcategories (الأقسام الفرعية - جديد)
    Route::controller(SubcategoryController::class)->group(function () {
        Route::get('/subcategories', 'index')->name('subcategories.index');
        Route::get('/get-subcategories', 'getSubcategoriesJson');
        Route::post('/subcategory-store', 'store')->name('subcategories.store');
        Route::post('/subcategory-update/{id}', 'update');
        Route::post('/subcategory-status/{id}', 'updateStatus');
        Route::delete('/subcategory-delete/{id}', 'destroy');
        Route::get('/subcategories/{id}/products', 'getProducts');
    });
    // 5. Products
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products-json', 'productsJson');
        Route::post('/products-store', 'store');
        Route::post('/products-update/{id}', 'update');
        Route::delete('/products-delete/{id}', 'destroy');
        Route::post('/products/delete-image', 'deleteImage');
        Route::post('/products-status/{id}', 'toggleStatus');
        // Reviews داخل ProductController
        Route::get('/products/{id}/reviews', [ProductController::class, 'getReviews']);
        Route::delete('/reviews/{id}', 'deleteReview');
    });

    // 6. Orders
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders-json', 'getOrdersJson')->name('orders.json');
        Route::post('/orders-status/{id}', 'updateStatus')->name('orders.updateStatus');
    });
    // 7. Offers
    Route::controller(OfferController::class)->group(function () {
        Route::get('/offers', 'index')->name('offers.index');
        Route::get('/offers-json', 'getOffersJson');
        Route::post('/offers-store', 'store')->name('offers.store');
        Route::delete('/offers-delete/{id}', 'destroy');
        Route::put('/offers-update/{id}', 'update');
        Route::patch('/offers-status/{id}', 'updateStatus');
    });

    // 8. Currencies
    Route::controller(CurrencyController::class)->group(function () {
        Route::get('/currencies', 'index')->name('currencies.index');
        Route::get('/currencies-json', 'getCurrenciesJson')->name('admin.currencies.json');
        Route::post('/currencies-store', 'store')->name('admin.currencies.store');
        Route::put('/currencies-update/{id}', 'update')->name('admin.currencies.update');
        Route::delete('/currencies-delete/{id}', 'destroy')->name('admin.currencies.destroy');
        Route::patch('/currencies-status/{id}', 'updateStatus')->name('admin.currencies.status');
    });

    // 9. Languages
    Route::controller(LanguageController::class)->group(function () {
        Route::get('/languages', 'index')->name('languages.index');
        Route::get('/languages-json', 'getLanguagesJson')->name('languages.json');
        Route::post('/languages', 'store')->name('languages.store');
        Route::put('/languages/{id}', [LanguageController::class, 'update'])->name('languages.update');
        Route::post('/languages/{id}/status', 'updateStatus')->name('languages.status');
        Route::delete('/languages/{id}', 'destroy')->name('languages.destroy');
    });

    // 10. Size Guides
    Route::controller(\App\Http\Controllers\Admin\SizeGuideController::class)->group(function () {
        Route::get('/size-guides', 'index')->name('size-guides.index');
        Route::get('/size-guides-json', 'getGuidesJson')->name('size-guides.json');
        Route::post('/size-guides-store', 'store')->name('size-guides.store');
        Route::post('/size-guides-update/{id}', 'update')->name('size-guides.update');
        Route::delete('/size-guides-delete/{id}', 'destroy')->name('size-guides.destroy');
    });
});

require __DIR__ . '/auth.php';
