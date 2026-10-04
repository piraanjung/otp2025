<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * โมเดลที่ใช้ trait BelongsToOrganization จะกรอง where org_id_fk อัตโนมัติ
 * แต่ 2 ตารางนี้ไม่เคยมีคอลัมน์ org_id_fk จึงเกิด
 * "Unknown column 'org_id_fk' in 'WHERE'" (เช่น หน้า admin/super_users/create)
 *
 * เขียนแบบ idempotent (เช็คตาราง/คอลัมน์ก่อน) เพื่อรันซ้ำบน production ได้อย่างปลอดภัย
 */
return new class extends Migration
{
    private array $tables = ['sequence_number', 'foodwaste_bin_stocks'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            if (! Schema::hasTable($name) || Schema::hasColumn($name, 'org_id_fk')) {
                continue;
            }

            Schema::table($name, function (Blueprint $table) {
                $table->unsignedBigInteger('org_id_fk')->nullable()->index();
            });

            // ข้อมูลเดิมมาจากยุคที่ระบบมีองค์กรเดียว: ถ้าตอนนี้มีองค์กรเดียวให้ผูกแถวเดิมกับองค์กรนั้น
            // (ถ้ามีหลายองค์กรจะไม่เดาให้ ปล่อยเป็น NULL เพื่อให้ผู้ดูแลกำหนดเอง)
            if (Schema::hasTable('organizations') && DB::table('organizations')->count() === 1) {
                DB::table($name)->whereNull('org_id_fk')->update([
                    'org_id_fk' => DB::table('organizations')->value('id'),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'org_id_fk')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->dropIndex(['org_id_fk']);
                    $table->dropColumn('org_id_fk');
                });
            }
        }
    }
};
