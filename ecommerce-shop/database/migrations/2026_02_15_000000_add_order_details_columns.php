<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Auto-converted from Schema::table() to raw ALTER TABLE
    // SQLite 3.7.17 on Vercel PHP 8.3 runtime doesn't support
    // Laravel 12's column introspection (table-valued pragma functions).
    $statements = [
        // orders
        "ALTER TABLE orders ADD COLUMN order_number varchar NULL",
        "ALTER TABLE orders ADD COLUMN customer_email varchar NULL",
        "ALTER TABLE orders ADD COLUMN customer_phone varchar NULL",
        "ALTER TABLE orders ADD COLUMN shipping_address text NULL",
        "ALTER TABLE orders ADD COLUMN billing_address text NULL",
        "ALTER TABLE orders ADD COLUMN subtotal float NOT NULL DEFAULT 0",
        "ALTER TABLE orders ADD COLUMN shipping_cost float NOT NULL DEFAULT 0",
        "ALTER TABLE orders ADD COLUMN total float NULL",
        "ALTER TABLE orders ADD COLUMN currency_code varchar(10) NOT NULL DEFAULT 'SAR'",
        "ALTER TABLE orders ADD COLUMN payment_status varchar NOT NULL DEFAULT 'pending'",
        "ALTER TABLE orders ADD COLUMN notes text NULL",
    ];

    foreach ($statements as $sql) {
        try {
            DB::statement($sql);
        } catch (\Throwable $e) {
            // Ignore duplicate column errors - idempotent
        }
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumnIfExists('order_number');
            $table->dropColumnIfExists('customer_email');
            $table->dropColumnIfExists('customer_phone');
            $table->dropColumnIfExists('shipping_address');
            $table->dropColumnIfExists('billing_address');
            $table->dropColumnIfExists('subtotal');
            $table->dropColumnIfExists('shipping_cost');
            $table->dropColumnIfExists('total');
            $table->dropColumnIfExists('currency_code');
            $table->dropColumnIfExists('payment_status');
            $table->dropColumnIfExists('notes');
        });
    }
};
