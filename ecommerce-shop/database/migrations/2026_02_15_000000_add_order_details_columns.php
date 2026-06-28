<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add new order columns if they don't exist
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->jsonb('shipping_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'billing_address')) {
                $table->jsonb('billing_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('orders', 'currency_code')) {
                $table->string('currency_code', 10)->default('SAR');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('pending');
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
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
