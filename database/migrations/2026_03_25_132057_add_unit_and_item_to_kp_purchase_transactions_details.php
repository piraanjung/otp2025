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
        Schema::table('kp_purchase_transactions_details', function (Blueprint $blueprint) {
            // 1. เพิ่ม Unit ID (Foreign Key ไปยังตารางหน่วย)
            if (!Schema::hasColumn('kp_purchase_transactions_details', 'kp_units_idfk')) {
                $blueprint->unsignedBigInteger('kp_units_idfk')->nullable()->after('kp_u_trans_no')->comment('ID หน่วยสินค้า');
            }

            // 2. เพิ่ม Item ID (Foreign Key ไปยังตารางขยะ/สินค้า)
            if (!Schema::hasColumn('kp_purchase_transactions_details', 'kp_recycle_item_id')) {
                $blueprint->unsignedBigInteger('kp_recycle_item_id')->nullable()->after('kp_units_idfk')->comment('ID ชนิดขยะ');
            }

            // 3. เพิ่ม Price Point ID (ถ้าต้องการอ้างอิงราคา ณ ขณะนั้น)
            if (!Schema::hasColumn('kp_purchase_transactions_details', 'kp_tbank_items_pricepoint_id')) {
                $blueprint->unsignedBigInteger('kp_tbank_items_pricepoint_id')->nullable()->after('kp_recycle_item_id');
            }

            // 4. เพิ่มคอลัมน์สำหรับเก็บ Foreign Key ไปยังตารางแม่ (Header)
            if (!Schema::hasColumn('kp_purchase_transactions_details', 'kp_purchase_trans_id')) {
                $blueprint->unsignedBigInteger('kp_purchase_trans_id')->nullable()->after('id')->comment('เชื่อมไปตาราง Header');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kp_purchase_transactions_details', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'kp_units_idfk',
                'kp_recycle_item_id',
                'kp_tbank_items_pricepoint_id',
                'kp_purchase_trans_id'
            ]);
        });
    }
};
