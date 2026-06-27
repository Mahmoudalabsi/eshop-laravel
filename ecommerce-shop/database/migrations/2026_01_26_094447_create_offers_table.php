<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {Schema::create('offers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('discount_value'); // القيمة الرقمية
    $table->enum('type', ['percentage', 'fixed']); // نسبة مئوية أو رقم ثابت
    $table->enum('scope', ['all', 'category', 'subcategory', 'product']); // نطاق العرض
    $table->unsignedBigInteger('target_id')->nullable(); // ID المستهدف (منتج أو قسم)
    $table->dateTime('starts_at');
    $table->dateTime('ends_at');
    $table->boolean('status')->default(1); // 1 = نشط، 0 = معطل
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
