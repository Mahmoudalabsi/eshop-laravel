<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Auto-converted from Schema::table() to raw ALTER TABLE
    // SQLite 3.7.17 on Vercel PHP 8.3 runtime doesn't support
    // Laravel 12's column introspection (table-valued pragma functions).
    $statements = [
        // products
        "ALTER TABLE products ADD COLUMN status integer NOT NULL DEFAULT 1",
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
