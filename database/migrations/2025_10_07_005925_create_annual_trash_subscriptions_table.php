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
        // 1. ตารางการสมัครบริการรายปี (Subscription)
        Schema::create('annual_trash_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_year'); // ปีงบประมาณ เช่น 2569
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // เชื่อมกับถังขยะ (อนุญาตให้เป็น NULL ได้ตอนเริ่มสมัคร)
            $table->foreignId('waste_bin_id')
                ->nullable()
                ->constrained('annual_trashs') // ตรวจสอบว่าตารางถังขยะชื่อนี้จริงๆ หรือไม่
                ->onDelete('set null');

            // เชื่อมกับอัตราค่าบริการ
            $table->foreignId('payrate_permonth_id_fk')
                ->nullable()
                ->constrained('kp_waste_bin_payrate_permonth') // แก้ชื่อให้ตรงกับที่คุณส่งมาก่อนหน้า
                ->onDelete('set null');

            $table->decimal('annual_fee', 10, 2); // ยอดรวมทั้งปี
            $table->decimal('month_fee', 10, 2);  // ยอดต่อเดือน
            $table->decimal('total_paid_amt', 10, 2)->default(0); // ยอดที่จ่ายสะสมมาแล้ว
            $table->string('status')->default('pending'); // pending, partially_paid, paid, overdue
            $table->timestamps();

            // ป้องกันการสมัครซ้ำ: 1 ถัง ต่อ 1 ปีงบประมาณ (ถ้ามีถัง)
            // หมายเหตุ: ถ้า waste_bin_id เป็น NULL จะไม่ติด Unique ตัวนี้ในบางฐานข้อมูล
            // $table->unique(['waste_bin_id', 'fiscal_year']);
        });

        // 2. ตารางบันทึกการชำระเงิน (Payments)
        Schema::create('annual_trash_payments', function (Blueprint $table) {
            $table->id();
            // ต้องชี้ไปที่ annual_trash_subscriptions ให้ตรงกับชื่อตารางด้านบน
            $table->foreignId('wbs_id')
                ->constrained('annual_trash_subscriptions')
                ->onDelete('cascade');

            $table->unsignedTinyInteger('pay_mon'); // เดือนที่ชำระ (1-12)
            $table->unsignedSmallInteger('pay_yr'); // ปีพ.ศ./ค.ศ. ที่ชำระ
            $table->decimal('amount_paid', 10, 2);
            $table->date('pay_date');
            $table->string('notes')->nullable();

            // เจ้าหน้าที่ผู้รับเงิน
            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();

            // ป้องกันการจ่ายซ้ำในเดือนเดียวกันของ Subscription นั้นๆ
            $table->unique(['wbs_id', 'pay_mon', 'pay_yr']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_trash_payments');
        Schema::dropIfExists('annual_trash_subscriptions');
    }
};
