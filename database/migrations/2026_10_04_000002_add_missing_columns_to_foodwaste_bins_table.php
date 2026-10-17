<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * model FoodAnnualTrash (ตาราง foodwaste_bins) ใช้คอลัมน์ u_pref_id_fk, bin_code_fk, iotbox_id_fk
 * แต่ migration สร้างตารางตอนแรกไม่มีคอลัมน์เหล่านี้ (เพิ่มด้วยมือใน DB พัฒนา)
 * จึงเกิด "Unknown column 'foodwaste_bins.u_pref_id_fk'" ที่หน้า foodwaste/users บน production
 *
 * idempotent: เช็คตารางและคอลัมน์ก่อนเพิ่ม รันซ้ำได้อย่างปลอดภัย (nullable ไม่กระทบข้อมูลเดิม)
 */
return new class extends Migration
{
    private array $columns = ['u_pref_id_fk', 'bin_code_fk', 'iotbox_id_fk'];

    public function up(): void
    {
        if (! Schema::hasTable('foodwaste_bins')) {
            return;
        }

        foreach ($this->columns as $column) {
            if (Schema::hasColumn('foodwaste_bins', $column)) {
                continue;
            }

            Schema::table('foodwaste_bins', function (Blueprint $table) use ($column) {
                $table->unsignedBigInteger($column)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('foodwaste_bins')) {
            return;
        }

        foreach ($this->columns as $column) {
            if (Schema::hasColumn('foodwaste_bins', $column)) {
                Schema::table('foodwaste_bins', function (Blueprint $table) use ($column) {
                    $table->dropIndex([$column]);
                    $table->dropColumn($column);
                });
            }
        }
    }
};
