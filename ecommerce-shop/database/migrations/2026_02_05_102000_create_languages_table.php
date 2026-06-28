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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم اللغة: العربية، الإنجليزية
            $table->string('code')->unique(); // الكود: ar, en, fr
            $table->string('flag')->nullable(); // العلم (Emoji): 🇸🇦، 🇺🇸
            $table->enum('direction', ['ltr', 'rtl'])->default('rtl'); // اتجاه النص
            $table->boolean('is_default')->default(false); // هل هذه اللغة الافتراضية
            $table->boolean('status')->default(true); // هل مفعلة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
