<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk')->comment('รหัสเทศบาล/องค์กร');
            $table->string('code', 50)->nullable()->comment('รหัสสถานี เช่น ST-01');
            $table->string('name')->comment('ชื่อสถานีผลิตน้ำ');
            $table->decimal('lat', 10, 7)->nullable()->comment('พิกัด ละติจูด');
            $table->decimal('lng', 10, 7)->nullable()->comment('พิกัด ลองจิจูด');

            // Config Threshold เกณฑ์เตือนภัยวิกฤตประจำสถานี (Dynamic Thresholds)
            $table->decimal('max_turbidity_ntu', 5, 2)->default(5.00)->comment('เกณฑ์ความขุ่นวิกฤต (NTU)');
            $table->decimal('min_ph', 3, 1)->default(6.5)->comment('ค่า pH ต่ำสุด');
            $table->decimal('max_ph', 3, 1)->default(8.5)->comment('ค่า pH สูงสุด');
            $table->decimal('max_tds_ppm', 6, 2)->default(300.00)->comment('เกณฑ์ TDS สูงสุด');
            $table->decimal('min_water_level_percent', 5, 2)->default(30.00)->comment('ระดับน้ำต่ำเตือนเร่งสูบ (%)');
            $table->decimal('max_water_level_percent', 5, 2)->default(95.00)->comment('ระดับน้ำสูงเตือนตัดปั๊ม (%)');

            // ค่า K แปลผลระยะไม้จุ่ม (สูตร: NTU = K / cm)
            $table->decimal('secchi_k_factor', 6, 2)->default(350.00)->comment('ค่า K สำหรับคำนวณไม้จุ่ม');

            $table->enum('status', ['normal', 'warning', 'danger'])->default('normal');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_stations');
    }
};