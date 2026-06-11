<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('kp_money_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
        $table->decimal('amount', 12, 2);

        // 🛡️ ระบบความปลอดภัย
        $table->string('verification_code', 6); // รหัส 6 หลักที่ User ต้องโชว์ให้ Admin
        $table->string('status')->default('pending'); // pending, ready, completed, rejected

        // 👥 ข้อมูลการรับแทน
        $table->boolean('is_proxy')->default(false);
        $table->string('proxy_name')->nullable();

        // 📅 การนัดหมายและบันทึก
        $table->date('payout_date')->nullable(); // วันอังคารที่นัดรับ
        $table->foreignId('admin_id')->nullable()->constrained('users'); // ใครเป็นคนจ่ายเงินจริง
        $table->timestamp('completed_at')->nullable(); // จ่ายเงินจริงเมื่อไหร่

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_money_requests');
    }
};
