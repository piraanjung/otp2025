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
        Schema::create('kp_purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kp_purchase_trans_id')->constrained('kp_purchase_transactions')->onDelete('cascade');
            $table->foreignId('kp_recycle_item_id')->constrained('kp_tbank_items')->onDelete('restrict');
            $table->foreignId('kp_tbank_items_pricepoint_id')->nullable()->constrained('kp_tbank_items_pricepoint')->onDelete('set null');
            $table->foreignId('recorder_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->decimal('amount_in_units', 10, 2); // ปริมาณ (น้ำหนัก/ชิ้น)
            $table->decimal('price_per_unit', 8, 2); // ราคาต่อหน่วยที่ใช้ ณ เวลาที่ซื้อ
            $table->decimal('amount', 10, 2); // ยอดเงินสำหรับรายการนี้
            $table->integer('points')->default(0); // คะแนนสำหรับรายการนี้
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_purchase_details');
    }
};
