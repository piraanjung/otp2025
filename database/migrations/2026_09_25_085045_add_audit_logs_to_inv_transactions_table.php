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
        Schema::table('inv_transactions', function (Blueprint $table) {
            // 1. สถานะ PENDING (ถ้ายังไม่มีฟิลด์บันทึกคนสร้าง สามารถเพิ่มตรงนี้ได้)
            // $table->unsignedBigInteger('requested_by')->nullable()->after('user_id_fk');
            // $table->timestamp('requested_at')->nullable();

            // 2. สถานะ APPROVED (อนุมัติ)
            if (!Schema::hasColumn('inv_transactions', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
            }

            // 3. สถานะ REJECTED (ไม่อนุมัติ)
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // 4. สถานะ CANCELED (ยกเลิก)
            $table->unsignedBigInteger('canceled_by')->nullable();
            $table->timestamp('canceled_at')->nullable();

            // 5. สถานะ DISPENSED (จ่ายพัสดุ / ตัดสต็อก)
            if (!Schema::hasColumn('inv_transactions', 'dispensed_by')) {
                $table->unsignedBigInteger('dispensed_by')->nullable();
                $table->timestamp('dispensed_at')->nullable();
            }

            // 6. สถานะ COMPLETED (ผู้ขอเบิกกดยืนยันรับของ)
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();

            // 7. สถานะ RETURNED (คืนพัสดุเข้าคลัง)
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->timestamp('returned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'approved_by', 'approved_at',
                'rejected_by', 'rejected_at',
                'canceled_by', 'canceled_at',
                'dispensed_by', 'dispensed_at',
                'completed_by', 'completed_at',
                'returned_by', 'returned_at',
            ]);
        });
    }
};