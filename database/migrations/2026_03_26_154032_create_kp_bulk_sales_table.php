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
    Schema::create('kp_bulk_sales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('org_id_fk')->constrained('organizations'); // สังกัด
        $table->date('sale_date'); // วันที่ขายให้โรงหลอม/ซาเล้งใหญ่
        $table->string('buyer_name'); // ชื่อร้านที่รับซื้อ (เช่น ร้านเฮียสมศักดิ์)

        // สรุปยอดเงิน
        $table->decimal('total_cost', 12, 2); // ต้นทุนรวม (เงินที่จ่ายซื้อจากชาวบ้านสะสมมา)
        $table->decimal('total_revenue', 12, 2); // ยอดขายรวม (เงินที่โรงหลอมจ่ายให้เรา)
        $table->decimal('profit_amount', 12, 2); // กำไรส่วนต่าง (ตัวนี้จะวิ่งเข้ากองทุนสวัสดิการ)

        $table->string('receipt_image')->nullable(); // รูปใบเสร็จจากโรงหลอม
        $table->foreignId('recorder_id')->constrained('users'); // Admin คนที่บันทึก
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_bulk_sales');
    }
};
