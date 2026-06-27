<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home and Browse Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/currency/{code}', [CurrencyController::class, 'setCurrency'])->name('currency.set');

// Static Pages Routes
Route::get('/privacy-policy', [\App\Http\Controllers\PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms-of-use', [\App\Http\Controllers\PageController::class, 'terms'])->name('pages.terms');
Route::get('/contact-us', [\App\Http\Controllers\PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact-us', [\App\Http\Controllers\PageController::class, 'contactSubmit'])->name('pages.contact.submit');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/on-offer', [ProductController::class, 'onOffer'])->name('products.onOffer');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Category Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Offer Routes
Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
Route::get('/api/offers-ticker', [OfferController::class, 'apiIndex'])->name('api.offers.ticker');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/api/cart', [CartController::class, 'getCartData'])->name('api.cart');

// Language Routes
Route::get('/language/{code}', [LanguageController::class, 'setLanguage'])->name('language.set');

// Checkout Routes (requires authentication)
// Checkout Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/callback', [CheckoutController::class, 'paymentCallback'])->name('checkout.callback');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Wishlist Routes
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Product Review Submission
    Route::post('/products/{product}/review', [\App\Http\Controllers\ProductController::class, 'submitReview'])->name('products.review');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Languages Management
    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('/languages-json', [LanguageController::class, 'getLanguagesJson'])->name('languages.json');
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::put('/languages/{id}', [LanguageController::class, 'update'])->name('languages.update');
    Route::post('/languages/{id}/status', [LanguageController::class, 'updateStatus'])->name('languages.status');
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');

    // Size Guides Management
    Route::get('/size-guides', [\App\Http\Controllers\SizeGuideController::class, 'adminIndex'])->name('size-guides.index');
    Route::get('/size-guides/create', [\App\Http\Controllers\SizeGuideController::class, 'adminCreate'])->name('size-guides.create');
    Route::post('/size-guides', [\App\Http\Controllers\SizeGuideController::class, 'adminStore'])->name('size-guides.store');
    Route::get('/size-guides/{id}/edit', [\App\Http\Controllers\SizeGuideController::class, 'adminEdit'])->name('size-guides.edit');
    Route::put('/size-guides/{id}', [\App\Http\Controllers\SizeGuideController::class, 'adminUpdate'])->name('size-guides.update');
    Route::delete('/size-guides/{id}', [\App\Http\Controllers\SizeGuideController::class, 'adminDestroy'])->name('size-guides.destroy');

    // Category Management (for Size Guides)
    Route::get('/categories', [CategoryController::class, 'adminIndex'])->name('categories.index');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'adminEdit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'adminUpdate'])->name('categories.update');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', function () { return view('auth.login'); })->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    // Registration Routes
    Route::get('register', function () { return view('auth.register'); })->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    
    // Password Reset Routes
    Route::get('forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', function ($token) { return view('auth.reset-password', ['token' => $token]); })->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

