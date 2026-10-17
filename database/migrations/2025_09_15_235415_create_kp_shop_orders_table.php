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
        Schema::create('kp_shop_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->foreignId('user_wpref_id')->constrained('kp_user_waste_preferences')->onDelete('cascade');
            $table->enum('order_type', ['points', 'cash']);
            $table->decimal('points_per_unit', 10, 2)->default(0); // คะแนนรวม
            $table->decimal('cash_per_unit', 10, 2)->default(0); // คะแนนรวม
            $table->decimal('total_points', 10, 2)->default(0); // คะแนนรวม
            $table->decimal('total_cash', 10, 2)->default(0); // เงินสดรวม
            $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending'); // สถานะคำสั่งซื้อ (pending, processing, completed, cancelled)
            $table->foreignId('recorder_id')->nullable()->constrained('staffs')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_shop_orders');
    }
};
