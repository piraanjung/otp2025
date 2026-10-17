<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('inv_item_details', function (Blueprint $table) {
        $table->id();

        // เชื่อมกับตารางแม่ (Catalog)
        $table->foreignId('inv_item_id_fk')->constrained('inv_items')->onDelete('cascade');
        $table->foreignId('supplier_id_fk')->constrained('suppliers')->onDelete('cascade');

        // ข้อมูล Lot/Batch
        $table->string('lot_number')->nullable();     // Lot การผลิต
        $table->string('reference_doc')->nullable();     // PO 
        $table->string('serial_number')->nullable();  // รหัสครุภัณฑ์ (ถ้ามี)
        
        // --- ส่วนสำคัญ: ปริมาณ ---
        // initial_qty: ขนาดบรรจุต่อขวด (เช่น 1000 ml)
        $table->decimal('initial_qty', 10, 2); 
        
        // current_qty: ปริมาณที่เหลือจริงในขวด (เช่น 450 ml)
        $table->decimal('current_qty', 10, 2); 
        
        $table->date('expire_date')->nullable();      // วันหมดอายุของขวดนี้
        $table->date('received_date')->nullable();    // วันที่รับของชิ้นนี้เข้ามา
        $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
        $table->foreignId('location_id_fk')->constrained('inv_locations')->onDelete('cascade');

        // สถานะของขวดนี้
        // ACTIVE=ปกติ, EMPTY=หมดแล้ว, EXPIRED=หมดอายุ, DISPOSED=ทิ้ง/กำจัดแล้ว
        $table->enum('status', ['ACTIVE', 'EMPTY', 'EXPIRED', 'DISPOSED'])->default('ACTIVE');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_item_details');
    }
};
