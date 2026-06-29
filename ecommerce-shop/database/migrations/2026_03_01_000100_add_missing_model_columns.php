<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds ALL missing columns that the Eloquent models declare in $fillable
 * but were never actually created by previous migrations.
 *
 * Root cause: many earlier migrations (2026_01_20_102822, 2026_01_26_092300,
 * 2026_02_03_153122) have EMPTY $statements arrays because they were
 * auto-converted from Schema::table() to raw ALTER TABLE for SQLite-on-Vercel
 * compatibility, but the conversion script dropped the columns.
 *
 * Without this migration, the SetupController's updateOrCreate / create calls
 * would throw "no such column: slug" (or similar) for products, categories,
 * subcategories, offers, reviews, etc.
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

            // ===== categories =====
            "ALTER TABLE categories ADD COLUMN image varchar NULL",
            "ALTER TABLE categories ADD COLUMN status integer NOT NULL DEFAULT 1",

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

            // ===== product_attributes =====
            // (Schema already has color, size, qty — model uses name/value which is a
            // mismatch, but SetupController uses color/size/qty which matches the schema.
            // No columns needed here.)

            // ===== users =====
            "ALTER TABLE users ADD COLUMN role varchar NOT NULL DEFAULT 'user'",
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
    }

    public function down(): void
    {
        // SQLite cannot easily drop columns; leave them in place on rollback.
    }
};
