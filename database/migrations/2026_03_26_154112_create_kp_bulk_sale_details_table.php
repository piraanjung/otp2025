<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('kp_bulk_sale_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bulk_sale_id')->constrained('kp_bulk_sales')->onDelete('cascade');
        $table->foreignId('kp_tbank_item_id')->constrained('kp_tbank_items'); // ประเภทขยะ

        $table->decimal('weight_kg', 10, 2); // น้ำหนักที่ขายได้จริง
        $table->decimal('price_per_unit', 10, 2); // ราคาที่โรงหลอมให้ต่อหน่วย
        $table->decimal('sub_total', 12, 2); // ยอดรวมของรายการนี้
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_bulk_sale_details');
    }
};
