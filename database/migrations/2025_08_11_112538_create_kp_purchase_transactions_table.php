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
        Schema::create('kp_purchase_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('kp_u_trans_no')->unique(); // เลขที่ธุรกรรม (เช่น T-0001)
            $table->foreignId('kp_user_w_pref_id_fk')->constrained('user_waste_preferences')->onDelete('cascade'); // ผู้ขาย (สมาชิก Keptkaya)
            $table->date('transaction_date');
            $table->decimal('total_weight', 10, 2)->default(0.00); // น้ำหนักรวม
            $table->decimal('total_amount', 10, 2)->default(0.00); // ยอดรวมเป็นเงิน
            $table->integer('total_points')->default(0); // คะแนนรวม
            $table->string('status')->default('completed'); // สถานะธุรกรรม (e.g., completed, cancelled)
            $table->foreignId('recorder_id')->nullable()->constrained('staffs', 'user_id')->onDelete('set null'); // ผู้บันทึก (พนักงาน/ผู้ดูแล)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_purchase_transactions');
    }
};
