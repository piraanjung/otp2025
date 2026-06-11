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
        Schema::create('kp_kiosk_unknown_items', function (Blueprint $table) {
            $table->id();
            
            // 🎯 เชื่อมโยงธุรกรรมหลัก (Foreign Key ไปหา kp_purchase_transactions)
            // ใช้ unsignedBigInteger เพื่อให้สอดคล้องกับ ID หลัก และทำ Index เพื่อการค้นหาที่รวดเร็ว
            $table->unsignedBigInteger('kp_purchase_trans_id')->comment('ไอดีอ้างอิงใบเสร็จ/ธุรกรรมหลัก');
            
            $table->unsignedBigInteger('org_id_fk')->nullable()->comment('ไอดีหน่วยงาน/องค์กร');
            $table->unsignedBigInteger('kiosk_id_fk')->default(1)->comment('ไอดีประจำตู้คีออส');
            
            // 👤 เก็บข้อมูลผู้ใช้งาน (ใช้ข้อความเป็น String เพื่อสอดรับกับโครงสร้าง USER_ID เช่น GUEST_8899 ฝั่งหน้าบ้าน)
            $table->string('user_id_fk', 50)->nullable()->comment('รหัสผู้ใช้งานที่หย่อนสิ่งแปลกปลอม');
            
            // 🤖 ข้อมูลวิเคราะห์จาก AI หน้าตู้ AIroBacT
            $table->string('detected_label')->comment('ชื่อคลาสขยะที่ AI อ่านได้ ณ ตอนนั้น');
            $table->integer('confidence_score')->default(0)->comment('ค่าความมั่นใจของ AI (0-100)');
            
            // 📸 พาร์ทรูปภาพขยะแปลกปลอม
            $table->string('image_path')->nullable()->comment('พาร์ทเก็บรูปภาพขยะสิ่งแปลกปลอมในโฟลเดอร์ public');
            
            // ⚙️ สถานะสำหรับหน้าจัดการของ Super Admin บน PI-OS
            $table->string('status', 30)->default('pending_review')->comment('สถานะ: pending_review, verified, rejected');
            
            $table->timestamps();

            // 🛠️ การทำดัชนี (Indexes) เพื่อป้องกันฐานข้อมูลหน่วงเวลาตู้ขยะดึงรายงานสรุป
            $table->index('kp_purchase_trans_id');
            $table->index('user_id_fk');
            $table->index('status');
            
            /* * 💡 หมายเหตุเรื่อง Foreign Key Constraint:
             * หากตาราง kp_purchase_transactions ของพี่ใช้ชื่อ Field หลักว่า 'id' 
             * พี่สามารถเปิดคอมเมนต์บรรทัดด้านล่างนี้เพื่อเปิดระบบล็อกความปลอดภัยระดับ Database ได้เลยครับ
             */
            // $table->foreign('kp_purchase_trans_id')->references('id')->on('kp_purchase_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_kiosk_unknown_items');
    }
};