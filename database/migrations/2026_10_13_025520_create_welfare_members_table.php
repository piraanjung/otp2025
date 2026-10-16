<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // ประเภทสมาชิกกองทุน
            // both = ขายขยะ + สมทบฌาปนกิจ, waste_only = ธนาคารขยะอย่างเดียว, contribution_only = ฝากเงินสมทบอย่างเดียว
            $table->enum('membership_type', ['both', 'waste_only', 'contribution_only'])->default('both');
            
            // สถานะในกองทุน
            // active = ปกติ, suspended = ค้างจ่ายเงินสมทบ/ระงับสิทธิ์ชั่วคราว, terminated = พ้นสภาพ
            $table->enum('status', ['active', 'suspended', 'terminated'])->default('active');
            
            $table->timestamp('registered_at')->useCurrent(); // วันที่สมัครเข้ากองทุน
            $table->text('note')->nullable(); // หมายเหตุ
            $table->timestamps();

            // ป้องกันไม่ให้ user คนเดิมสมัครซ้ำในองค์กรเดียวกัน
            $table->unique(['org_id_fk', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_members');
    }
};