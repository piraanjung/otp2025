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
                ->constrained('annual_trashs')
                ->onDelete('cascade');

            // 🛠️ แก้ไขจุดที่มีปัญหา: แยกการประกาศคอลัมน์ออกมาก่อน
            // (หากตารางแม่ใช้ไอดีประเภท Integer ธรรมดา ให้เปลี่ยนจาก unsignedBigInteger เป็น unsignedInteger ให้ตรงกัน)
            $table->unsignedBigInteger('payrate_permonth_id_fk')->nullable();

            $table->decimal('annual_fee', 10, 2); // ยอดรวมทั้งปี
            $table->decimal('month_fee', 10, 2);  // ยอดต่อเดือน
            $table->decimal('total_paid_amt', 10, 2)->default(0); // ยอดที่จ่ายสะสมมาแล้ว
            $table->string('status')->default('pending'); // pending, partially_paid, paid, overdue
            $table->timestamps();

            // 🛠️ แก้ไขจุดที่มีปัญหา: สั่งผูก Foreign Key แยกบรรทัดตามโครงสร้างมาตรฐาน
            $table->foreign('payrate_permonth_id_fk')
                ->references('id')
                ->on('annual_trash_payrate_permonth')
                ->onDelete('cascade');
        });

        // 2. ตารางบันทึกการชำระเงิน (Payments) คงโครงสร้างเดิมไว้ตามคำสั่งของคุณ
        Schema::create('annual_trash_payments', function (Blueprint $table) {
            $table->id();
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
                ->onDelete('cascade');

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
