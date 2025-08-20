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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id(); // Primary Key (id)

            // หาก org_id_fk หมายถึง parent organization (องค์กรแม่)
            $table->string('org_code')->unique();

            $table->string('org_type_name')->nullable(); // ชื่อประเภทองค์กร
                        $table->string('org_short_type_name')->nullable(); // ชื่อย่อประเภทองค์กร
            $table->string('org_name'); // ชื่อองค์กรเต็ม 
            $table->text('org_address')->nullable(); // ที่อยู่องค์กร

            // Foreign Keys สำหรับข้อมูลภูมิศาสตร์ (ต้องมั่นใจว่าตารางเหล่านี้ถูกสร้างก่อน)
            $table->foreignId('org_zone_id_fk')->nullable()->constrained('zones')->onDelete('set null');
            $table->foreignId('org_tambon_id_fk')->nullable()->constrained('tambons')->onDelete('set null');
            $table->foreignId('org_district_id_fk')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('org_province_id_fk')->nullable()->constrained('provinces')->onDelete('set null');

            $table->string('org_route')->nullable(); // เส้นทาง/สาย (ถ้ามี)
            $table->string('org_zipcode', 10)->nullable(); // รหัสไปรษณีย์
            $table->string('org_phone')->nullable(); // เบอร์โทรศัพท์องค์กร

            $table->string('org_dept_name')->nullable(); // ชื่อแผนก (ถ้าองค์กรมีแผนกย่อย)
            $table->string('org_dept_short_name')->nullable(); // ชื่อย่อแผนก
            $table->string('org_dept_phone')->nullable(); // เบอร์โทรศัพท์แผนก

            $table->string('org_head_name')->nullable(); // ชื่อหัวหน้าองค์กร
            $table->string('fin_head_name')->nullable(); // ชื่อหัวหน้าฝ่ายการเงิน
            $table->string('tw_head_name')->nullable(); // ชื่อหัวหน้าฝ่าย TW (ไม่แน่ใจว่า TW คืออะไร)

            // ฟิลด์สำหรับเก็บ Path/URL รูปภาพ
            $table->string('fin_head_license_img')->nullable(); // รูปภาพใบอนุญาตหัวหน้าฝ่ายการเงิน
            $table->string('org_head_license_img')->nullable(); // รูปภาพใบอนุญาตหัวหน้าองค์กร
            $table->string('tw_head_license_img')->nullable(); // รูปภาพใบอนุญาตหัวหน้าฝ่าย TW
            $table->string('org_logo_img')->nullable(); // รูปภาพโลโก้องค์กร

            $table->string('meternumbercode')->nullable(); // รหัสสำหรับเลขมิเตอร์ (ถ้ามีรูปแบบเฉพาะขององค์กร)
            $table->string('org_database')->nullable(); // ข้อมูลเกี่ยวกับฐานข้อมูลขององค์กร (ถ้ามี)
            $table->integer('cutmeter_count')->default(0); // จำนวนครั้งที่ตัดมิเตอร์ (ถ้าเกี่ยวข้อง)
            $table->float('vat', 5, 2)->default(0.00); // อัตราภาษีมูลค่าเพิ่ม (เช่น 7.00)

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
