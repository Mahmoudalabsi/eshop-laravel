<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds missing columns to `orders` and `order_items` tables that the Order
 * and OrderItem models already declare in $fillable but were never created
 * by previous migrations. Without this, the SetupController's Order::create
 * call (which sets `tax`) would throw a SQL "no such column: tax" error.
 *
 * Idempotent: each ALTER TABLE is wrapped in try/catch so re-running on
 * databases that already have the columns is a no-op (important for the
 * SQLite-on-Vercel path where migrations run on every cold start).
 *
 * Applies to: ecommerce-eshop (storefront)
 */
return new class extends Migration
{
    public function up(): void
    {
        $statements = [
            // orders — extra columns expected by App\Models\Order::$fillable
            "ALTER TABLE orders ADD COLUMN tax float NOT NULL DEFAULT 0",
            "ALTER TABLE orders ADD COLUMN payment_method varchar NULL",
            "ALTER TABLE orders ADD COLUMN tracking_number varchar NULL",
            "ALTER TABLE orders ADD COLUMN shipped_at datetime NULL",
            "ALTER TABLE orders ADD COLUMN delivered_at datetime NULL",

            // order_items — sku column expected by App\Models\OrderItem::$fillable
            "ALTER TABLE order_items ADD COLUMN sku varchar NULL",
        ];

        foreach ($statements as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Ignore duplicate column errors - idempotent
            }
        }
    }

    public function down(): void
    {
        // SQLite cannot easily drop columns; leave them in place on rollback.
    }
};
