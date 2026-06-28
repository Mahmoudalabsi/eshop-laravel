<?php

use App\Models\User;
use App\Models\Language;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Create or reset the admin user.
 * Usage: php artisan admin:create
 */
Artisan::command('admin:create {--email=admin@elegance.com} {--password=admin123} {--name=مدير النظام}', function () {
    $email = $this->option('email');
    $password = $this->option('password');
    $name = $this->option('name');

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name'     => $name,
            'password' => Hash::make($password),
            'role'     => 'admin',
            'status'   => 1,
        ]
    );

    $this->info("✅ Admin user ready:");
    $this->line("   Name:     {$user->name}");
    $this->line("   Email:    {$user->email}");
    $this->line("   Password: {$password}");
    $this->line("   Role:     {$user->role}");
})->purpose('Create or reset the admin user (admin@elegance.com / admin123 by default)');

/**
 * Quick stats summary.
 * Usage: php artisan shop:stats
 */
Artisan::command('shop:stats', function () {
    $this->info('🛍️  Elegance Fashion — Shop Stats');
    $this->line(str_repeat('-', 50));
    $this->line("Users:        \t" . \App\Models\User::count() . " (admins: " . \App\Models\User::where('role', 'admin')->count() . ")");
    $this->line("Categories:   \t" . \App\Models\Category::count());
    $this->line("Subcategories:\t" . \App\Models\Subcategory::count());
    $this->line("Products:     \t" . \App\Models\Product::count());
    $this->line("Orders:       \t" . \App\Models\Order::count());
    $this->line("Reviews:      \t" . \App\Models\Review::count());
    $this->line("Offers:       \t" . \App\Models\Offer::count());
    $this->line("Currencies:   \t" . \App\Models\Currency::count());
    $this->line("Languages:    \t" . Language::count());
    $this->line("Wishlists:    \t" . \App\Models\Wishlist::count());
})->purpose('Show quick shop stats summary');
