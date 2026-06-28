<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\OrderController;

Route::prefix('v1')->group(function () {
    // مسارات عامة
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/products', [StoreController::class, 'getProducts']);
    Route::get('/products/{id}', [StoreController::class, 'getProduct']);
    Route::get('/categories', [StoreController::class, 'getCategories']);
    Route::get('/categories/{id}', [StoreController::class, 'getCategory']);
    Route::get('/currencies', [StoreController::class, 'getCurrencies']);
    Route::get('/offers', [StoreController::class, 'getOffers']);
    Route::get('/filters', [StoreController::class, 'getFilters']);
    Route::get('/languages', [LanguageController::class, 'index']); // جلب جميع اللغات (للأدمن والمستخدم)
    Route::get('/languages/active', [LanguageController::class, 'getActive']); // اللغات المفعلة فقط

    // مسارات تحتاج تسجيل دخول (Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // السلة
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/add', [CartController::class, 'store']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);

        // الطلبات والبروفايل
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [StoreController::class, 'getUserOrders']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/profile/update', [StoreController::class, 'updateProfile']);

        // المفضلات
        Route::get('/wishlist', [StoreController::class, 'getWishlist']);
        Route::post('/wishlist/toggle', [StoreController::class, 'toggleWishlist']);

        // التقييمات
        Route::post('/products/review', [StoreController::class, 'submitReview']);

        // إدارة اللغات (Admin)
        Route::post('/languages', [LanguageController::class, 'store']);
        Route::put('/languages/{id}', [LanguageController::class, 'update']);
        Route::post('/languages/{id}/status', [LanguageController::class, 'updateStatus']);
        // Size Guides (Admin)
        Route::apiResource('size-guides', \App\Http\Controllers\Api\SizeGuideController::class);

        // Categories (Admin)
        Route::put('/categories/{id}', [StoreController::class, 'updateCategory']);
    });

    Route::get('/size-guides', [\App\Http\Controllers\Api\SizeGuideController::class, 'index']);
    Route::get('/size-guides/{id}', [\App\Http\Controllers\Api\SizeGuideController::class, 'show']);

    // Public Checkout
    Route::post('/checkout', [StoreController::class, 'placeOrder']);
});
