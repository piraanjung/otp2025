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
        Schema::create('tw_notify_staff', function (Blueprint $table) {
            $table->foreignId('notify_id')->constrained('tw_notifies')->onDelete('cascade');
            // ID ของ Staff (User ที่เป็น Staff)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // สถานะเฉพาะของ Staff แต่ละคนต่องานนั้น
            $table->enum('staff_status', ['accepted', 'working', 'completed', 'rejected'])->default('accepted');

            // กำหนดให้ notify_id และ user_id เป็น Primary Key ร่วมกัน เพื่อไม่ให้ Staff รับงานซ้ำได้
            $table->primary(['notify_id', 'user_id']); 
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
        Schema::dropIfExists('tw_notify_staff');
    }
};
