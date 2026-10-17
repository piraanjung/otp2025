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
        Schema::create('kp_shop_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kp_shop_order_id')->constrained('kp_shop_orders')->onDelete('cascade');
            $table->foreignId('kp_shop_product_id')->constrained('kp_shop_products')->onDelete('restrict');
            $table->enum('order_type', ['points', 'cash']);
            $table->integer('quantity'); // จำนวนสินค้า
            $table->decimal('points_at_purchase', 10, 2)->default(0); // คะแนน ณ ตอนที่ซื้อ
            $table->decimal('cash_at_purchase', 10, 2)->default(0); // เงินสด ณ ตอนที่ซื้อ
            $table->enum('status', ['pending', 'complete', 'cancled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_shop_order_details');
    }
};

