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
        Schema::create('issue_types', function (Blueprint $table) {
            $table->id();
            $table->string('system_type');               // กลุ่มระบบ เช่น water, recycle_trash, food_waste, public_health
            $table->string('name');                      // ชื่อประเภทปัญหา
            $table->boolean('is_active')->default(true);  // สถานะเปิด/ปิดใช้งาน (true = แสดงในตัวเลือก)
            $table->boolean('is_suggested')->default(false); // ธงระบุว่าเป็นข้อเสนอแนะใหม่จาก user หรือไม่
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_types');
    }
};
