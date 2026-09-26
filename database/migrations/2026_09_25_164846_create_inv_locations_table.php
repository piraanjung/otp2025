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
        Schema::create('inv_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk'); // แยกตามองค์กร (จำเป็นเพื่อความปลอดภัยของ Multi-tenant)
            $table->string('department')->nullable(); // พิมพ์ชื่อแผนกตรงๆ เช่น "แผนกประปา", "แผนกแล็บ" (ไม่ต้องมีตารางแผนก)
            $table->string('location'); // ชื่อพื้นที่จัดเก็บ เช่น "ตู้เคมี A1"
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_locations');
    }
};
