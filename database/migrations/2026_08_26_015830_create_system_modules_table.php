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
        Schema::create('system_modules', function (Blueprint $table) {
            $table->id();
            $table->string('system_type')->unique(); // เช่น water, recycle_trash, food_waste, public_health
            $table->string('title');                 // ชื่อหมวดหมู่ เช่น งานระบบน้ำประปา
            $table->string('icon')->default('📌');   // ไอคอน หรือ emoji
            $table->text('description')->nullable(); // คำอธิบายสั้นๆ
            $table->string('color')->default('primary'); // สีธีมการ์ด เช่น primary, success, warning, danger
            $table->boolean('is_active')->default(true); // ปิด/เปิด การใช้งานหมวดหมู่นี้
            $table->integer('sort_order')->default(0);   // ลำดับการแสดงผล
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_modules');
    }
};
