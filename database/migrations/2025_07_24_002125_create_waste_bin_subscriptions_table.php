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
        Schema::create('waste_bin_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_bin_id')->constrained('waste_bins')->onDelete('cascade');
            $table->unsignedSmallInteger('fiscal_year'); // ปีงบประมาณที่สมัคร (เช่น 2024)
            $table->decimal('annual_fee', 10, 2); // ค่าธรรมเนียมรายปีทั้งหมด
            $table->decimal('month_fee', 10, 2); // ค่าธรรมเนียมรายเดือน (annual_fee / 12)
            $table->decimal('total_paid_amt', 10, 2)->default(0); // ยอดรวมที่ชำระแล้วสำหรับปีนั้น
            $table->string('status')->default('pending'); // pending, partially_paid, paid, overdue
            $table->timestamps();

            // Unique constraint to ensure one subscription per bin per fiscal year
            $table->unique(['waste_bin_id', 'fiscal_year']);
        });

        Schema::create('waste_bin_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wbs_id')->constrained('waste_bin_subscriptions')->onDelete('cascade'); // waste_bin_subscription_id
            $table->unsignedTinyInteger('pay_mon'); // เดือนที่ชำระ (1-12)
            $table->unsignedSmallInteger('pay_yr'); // ปีของเดือนที่ชำระ (อาจไม่ตรงกับ fiscal_year ถ้าค้างชำระ)
            $table->decimal('amount_paid', 10, 2); // จำนวนเงินที่ชำระในงวดนี้
            $table->date('pay_date'); // วันที่ชำระเงิน
            $table->string('notes')->nullable(); // บันทึกเพิ่มเติม
            $table->foreignId('staff_id')->nullable()->constrained('users'); // เจ้าหน้าที่ที่รับชำระ
            $table->timestamps();

            // Unique constraint to ensure one payment record per subscription per month
            $table->unique(['wbs_id', 'pay_mon', 'pay_yr']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_bin_payments');
        Schema::dropIfExists('waste_bin_subscriptions');
    }
};
