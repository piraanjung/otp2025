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
        Schema::create('kiosks', function (Blueprint $table) {
            // กำหนดให้ id เป็น String และเป็น Primary Key (เช่น 'SLAVE_01')
            $table->string('id')->primary();

            // ชื่อสถานที่ตั้ง หรือชื่อตู้
            $table->unsignedBigInteger('org_id_fk');
            $table->string('name');

            // พิกัด GPS (ใช้ Decimal เพื่อความแม่นยำ)
            // (10, 8) คือความละเอียดมาตรฐาน GPS (Latitude)
            // (11, 8) คือความละเอียดมาตรฐาน GPS (Longitude)
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();

            // สถานะของตู้
            $table->enum('status', ['idle', 'active', 'maintenance', 'offline'])->default('offline');

            // เก็บ ID ของ User ที่กำลังใช้งานอยู่ (nullable เพราะปกติจะไม่มีใครใช้)
            // สมมติว่าตาราง users ใช้ id เป็น BigInteger (ค่า default ของ Laravel)
            $table->unsignedBigInteger('current_user_id')->nullable();

            // จำนวนขยะสะสมในตู้ (เอาไว้แจ้งเตือนเมื่อตู้เต็ม)
            $table->integer('total_waste_count')->default(0);

            // เก็บเวลาล่าสุดที่บอร์ด IoT ส่งสัญญาณมา (Heartbeat)
            $table->timestamp('last_online_at')->nullable();

            $table->timestamps();

            // (Optional) สร้าง Foreign Key เชื่อมกับตาราง users
            // ถ้าลบ user ทิ้ง ให้ set ช่องนี้เป็น null
            $table->foreign('current_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('org_id_fk')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosks');
    }
};
