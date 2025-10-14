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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('machine_id')->unique();
            $table->foreignId('current_user_active_id')->constrained('users')->onDelete('cascade');            
            $table->tinyInteger('has_new_object')->comment('สถานะขวดใหม่ (0: ไม่มี, 1: แจ้งเตือนแล้ว/รอ AI, 2: AI ตัดสินใจแล้ว)');
            $table->tinyInteger('pending_command')->comment('คำสั่งที่ AI ตัดสินใจแล้ว (null: ไม่มีคำสั่ง, 0: ACCEPT, 1: REJECT)');
            $table->enum('buycomplete', ['0', '1'])->nullable();
    
            // คอลัมน์ใหม่: 0 = ไม่พร้อม, 1 = พร้อม (ตื่น/เชื่อมต่อได้)
            $table->boolean('machine_ready')->default(0); 
            
            // คอลัมน์ timestamp สำหรับ heartbeat ล่าสุด
            $table->timestamp('last_heartbeat_at')->nullable();
            // ...
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
        Schema::dropIfExists('machines');
    }
};
