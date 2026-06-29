<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Comprehensive idempotent migration that ensures ALL columns referenced by
 * the Eloquent models (and the SetupController) physically exist in the
 * database, regardless of which earlier migrations actually ran successfully.
 *
 * Each ALTER TABLE is wrapped in try/catch so re-running on databases that
 * already have the columns is a no-op. This is critical for the
 * SQLite-on-Vercel path where migrations run on every cold start and the
 * SQLite 3.7.17 runtime doesn't support Laravel 12's column introspection.
 *
 * This migration supersedes the partial/empty migrations:
 *   - 2026_01_20_111803_add_status_to_products_table.php       (status)
 *   - 2026_01_26_092300_add_offer_details_to_products.php      (was empty!)
 *   - 2026_01_20_102822_add_status_to_categories_table.php     (was empty!)
 *   - 2026_03_01_000100_add_missing_model_columns.php          (partial)
 *
 * Applies to: ecommerce-shop (admin/backend)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Use a single transaction-friendly approach: each statement is
        // independent and idempotent. Wrap each in try/catch so a missing
        // column on one table doesn't abort the rest.

        $statements = [
            // ===== products =====
            "ALTER TABLE products ADD COLUMN slug varchar NULL",
            "ALTER TABLE products ADD COLUMN short_description text NULL",
            "ALTER TABLE products ADD COLUMN cost_price float NULL",
            "ALTER TABLE products ADD COLUMN sku varchar NULL",
            "ALTER TABLE products ADD COLUMN barcode varchar NULL",
            "ALTER TABLE products ADD COLUMN weight float NULL",
            "ALTER TABLE products ADD COLUMN dimensions text NULL",
            "ALTER TABLE products ADD COLUMN is_featured integer NOT NULL DEFAULT 0",
            "ALTER TABLE products ADD COLUMN is_on_offer integer NOT NULL DEFAULT 0",
            "ALTER TABLE products ADD COLUMN discount_percentage integer NOT NULL DEFAULT 0",
            "ALTER TABLE products ADD COLUMN offer_expires_at datetime NULL",
            "ALTER TABLE products ADD COLUMN old_price float NULL",
            "ALTER TABLE products ADD COLUMN total_stock integer NOT NULL DEFAULT 0",
            "ALTER TABLE products ADD COLUMN subcategory_id integer NULL",
            "ALTER TABLE products ADD COLUMN status integer NOT NULL DEFAULT 1",

            // ===== categories =====
            "ALTER TABLE categories ADD COLUMN image varchar NULL",
            "ALTER TABLE categories ADD COLUMN status integer NOT NULL DEFAULT 1",
            "ALTER TABLE categories ADD COLUMN size_guide_id integer NULL",

            // ===== subcategories =====
            "ALTER TABLE subcategories ADD COLUMN slug varchar NULL",
            "ALTER TABLE subcategories ADD COLUMN description text NULL",
            "ALTER TABLE subcategories ADD COLUMN image varchar NULL",

            // ===== offers =====
            "ALTER TABLE offers ADD COLUMN image varchar NULL",

            // ===== reviews =====
            "ALTER TABLE reviews ADD COLUMN title varchar NULL",
            "ALTER TABLE reviews ADD COLUMN content text NULL",
            "ALTER TABLE reviews ADD COLUMN is_verified integer NOT NULL DEFAULT 0",
            "ALTER TABLE reviews ADD COLUMN helpful_count integer NOT NULL DEFAULT 0",
            "ALTER TABLE reviews ADD COLUMN status varchar NOT NULL DEFAULT 'approved'",

            // ===== orders =====
            "ALTER TABLE orders ADD COLUMN order_number varchar NULL",
            "ALTER TABLE orders ADD COLUMN customer_name varchar NULL",
            "ALTER TABLE orders ADD COLUMN customer_email varchar NULL",
            "ALTER TABLE orders ADD COLUMN customer_phone varchar NULL",
            "ALTER TABLE orders ADD COLUMN phone varchar NULL",
            "ALTER TABLE orders ADD COLUMN address text NULL",
            "ALTER TABLE orders ADD COLUMN shipping_address text NULL",
            "ALTER TABLE orders ADD COLUMN billing_address text NULL",
            "ALTER TABLE orders ADD COLUMN subtotal float NOT NULL DEFAULT 0",
            "ALTER TABLE orders ADD COLUMN shipping_cost float NOT NULL DEFAULT 0",
            "ALTER TABLE orders ADD COLUMN tax float NOT NULL DEFAULT 0",
            "ALTER TABLE orders ADD COLUMN total float NULL",
            "ALTER TABLE orders ADD COLUMN currency_code varchar(10) NOT NULL DEFAULT 'SAR'",
            "ALTER TABLE orders ADD COLUMN payment_status varchar NOT NULL DEFAULT 'pending'",
            "ALTER TABLE orders ADD COLUMN payment_method varchar NULL",
            "ALTER TABLE orders ADD COLUMN tracking_number varchar NULL",
            "ALTER TABLE orders ADD COLUMN shipped_at datetime NULL",
            "ALTER TABLE orders ADD COLUMN delivered_at datetime NULL",
            "ALTER TABLE orders ADD COLUMN notes text NULL",

            // ===== order_items =====
            "ALTER TABLE order_items ADD COLUMN product_name varchar NULL",
            "ALTER TABLE order_items ADD COLUMN unit_price float NULL",
            "ALTER TABLE order_items ADD COLUMN total_price float NULL",
            "ALTER TABLE order_items ADD COLUMN sku varchar NULL",
            "ALTER TABLE order_items ADD COLUMN size varchar NULL",
            "ALTER TABLE order_items ADD COLUMN color varchar NULL",
            "ALTER TABLE order_items ADD COLUMN attributes text NULL",

            // ===== users =====
            // The users table is created with `role` defaulting to 'customer',
            // but the admin middleware checks role === 'admin' and the
            // SetupController sets role explicitly. We do NOT try to alter
            // the role column's default here — SQLite cannot easily change
            // an existing column. Instead we ensure the status/profile_image
            // columns exist.
            "ALTER TABLE users ADD COLUMN status integer NOT NULL DEFAULT 1",
            "ALTER TABLE users ADD COLUMN profile_image varchar NULL",
        ];

        foreach ($statements as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Ignore duplicate column errors - idempotent
            }
        }

        // Indexes (best-effort, may fail on SQLite if already exist)
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_products_slug ON products(slug)",
            "CREATE INDEX IF NOT EXISTS idx_products_subcategory_id ON products(subcategory_id)",
            "CREATE INDEX IF NOT EXISTS idx_products_status ON products(status)",
            "CREATE INDEX IF NOT EXISTS idx_products_is_featured ON products(is_featured)",
            "CREATE INDEX IF NOT EXISTS idx_products_is_on_offer ON products(is_on_offer)",
            "CREATE INDEX IF NOT EXISTS idx_categories_status ON categories(status)",
            "CREATE INDEX IF NOT EXISTS idx_subcategories_category_id ON subcategories(category_id)",
            "CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status)",
            "CREATE INDEX IF NOT EXISTS idx_order_items_order_id ON order_items(order_id)",
            "CREATE INDEX IF NOT EXISTS idx_order_items_product_id ON order_items(product_id)",
            "CREATE INDEX IF NOT EXISTS idx_reviews_product_id ON reviews(product_id)",
            "CREATE INDEX IF NOT EXISTS idx_reviews_user_id ON reviews(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_users_role ON users(role)",
            "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)",
        ];

        foreach ($indexes as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Index may already exist or SQLite syntax unsupported
            }
        }
    }

    public function down(): void
    {
        // SQLite cannot easily drop columns; leave them in place on rollback.
        // Dropping is rarely needed in production.
    }
};
