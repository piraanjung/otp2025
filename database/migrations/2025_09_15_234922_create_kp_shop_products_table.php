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
        Schema::create('kp_shop_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->foreignId('kp_shop_category_id')->constrained('kp_shop_categories')->onDelete('restrict');
            $table->decimal('point_price', 10, 2)->default(0); // เพิ่มราคาแต้ม
            $table->decimal('cash_price', 10, 2)->default(0); // เพิ่มราคาเงินสด
            $table->integer('stock')->default(0); // เพิ่มคอลัมน์สต็อก
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_shop_products');
    }
};
