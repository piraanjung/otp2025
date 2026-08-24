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
    Schema::create('kp_welfare_payouts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('org_id_fk')->constrained('organizations'); // สังกัดเทศบาล/องค์กร
        $table->string('deceased_name'); // ชื่อสมาชิกที่เสียชีวิต
        $table->string('beneficiary_name'); // ชื่อทายาทผู้รับเงิน
        $table->decimal('amount', 12, 2); // จำนวนเงินที่ช่วย
        $table->string('death_certificate_img')->nullable(); // รูปใบมรณบัตร
        $table->text('note')->nullable(); // หมายเหตุ

        // ระบบอนุมัติ
        $table->foreignId('requester_id')->constrained('users'); // เจ้าหน้าที่ผู้แจ้งเรื่อง
        $table->foreignId('approver_id')->nullable()->constrained('users'); // ผู้บริหารที่อนุมัติ
        $table->string('status')->default('pending'); // pending, approved, paid
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_welfare_payouts');
    }
};
