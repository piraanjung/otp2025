<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWasteTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waste_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อประเภทขยะ เช่น "ขวดพลาสติก PET ใส"
            $table->foreignId('waste_group_id')
                  ->nullable() // กำหนดให้เป็น nullable ถ้าประเภทขยะอาจจะยังไม่มีกลุ่ม
                  ->constrained() // สร้าง foreign key constraint ไปยังตาราง waste_groups
                  ->onDelete('set null');// ถ้ากลุ่มถูกลบ, waste_group_id ใน waste_types จะถูกตั้งเป็น null
            $table->string('default_unit')->default('kg'); // หน่วยเริ่มต้นที่ใช้บ่อยที่สุด (kg, piece)

            // ราคาสำหรับหน่วย 'kg'
            $table->decimal('factory_buy_price_per_kg', 10, 2)->default(0.00)->nullable(); // ราคาที่โรงงานซื้อจากคุณ (ต่อ กก.)
            $table->decimal('member_buy_price_per_kg', 10, 2)->default(0.00)->nullable(); // ราคาที่คุณซื้อจากสมาชิก (ต่อ กก.)

            // ราคาสำหรับหน่วย 'piece' หรือ 'bottle'
            $table->decimal('factory_buy_price_per_piece', 10, 2)->default(0.00)->nullable(); // ราคาที่โรงงานซื้อจากคุณ (ต่อ ชิ้น)
            $table->decimal('member_buy_price_per_piece', 10, 2)->default(0.00)->nullable(); // ราคาที่คุณซื้อจากสมาชิก (ต่อ ชิ้น)

            $table->text('description')->nullable();
            $table->boolean('is_recyclable')->default(true); // เป็นขยะรีไซเคิลหรือไม่
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
        Schema::dropIfExists('waste_types');
    }
}
