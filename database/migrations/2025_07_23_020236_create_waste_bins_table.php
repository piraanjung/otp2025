<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWasteBinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kp_waste_bins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้ใช้เจ้าของถังขยะ
            $table->string('bin_code')->unique()->nullable(); // รหัสถังขยะ (ถ้ามี)
            $table->string('bin_type')->nullable(); // ประเภทถัง (เช่น พลาสติก, แก้ว, ทั่วไป)
            $table->string('location_description')->nullable(); // รายละเอียดตำแหน่งที่ตั้งของถัง (เช่น หน้าบ้าน, ข้างรั้ว)
            $table->decimal('latitude', 10, 7)->nullable(); // ละติจูดของถัง
            $table->decimal('longitude', 10, 7)->nullable(); // ลองจิจูดของถัง
            $table->enum('status', ['active', 'inactive', 'damaged', 'removed'])->default('active'); // สถานะโดยรวมของถัง
            $table->boolean('is_active_for_annual_collection')->default(true); // สถานะเฉพาะสำหรับการเก็บรายปี (ใช้งานจริง)
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
        Schema::dropIfExists('kp_waste_bins');
    }
}