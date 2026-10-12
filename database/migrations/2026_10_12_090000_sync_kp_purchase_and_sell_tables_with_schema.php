<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * ปรับโครงสร้างตารางรับซื้อ/ขายขยะบน DB ที่ migrate ไว้ก่อน commit f1a5962
 * ให้ตรงกับโครงสร้างปัจจุบัน (envsogo_database.sql)
 *
 * commit f1a5962 แก้ไฟล์ create migration เดิมโดยตรง DB ที่รัน migration ไปแล้ว
 * จึงยังเป็นโครงสร้างเก่า ทุกขั้นตอนเช็กโครงสร้างจริงก่อน รันซ้ำหรือรันกับ DB ที่ถูกต้องแล้วจะไม่เปลี่ยนอะไร
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->syncPurchaseTransactions();
        $this->syncSellTransactions();
        $this->syncSellDetails();
    }

    /**
     * แปลงข้อมูลไปแล้ว (pref id -> users.id) ย้อนกลับไม่ได้
     */
    public function down(): void
    {
    }

    private function syncPurchaseTransactions(): void
    {
        $t = 'kp_purchase_transactions';

        // 1. ผู้ขาย: kp_user_w_pref_id_fk (kp_user_waste_preferences.id) -> user_id (users.id)
        if (! Schema::hasColumn($t, 'user_id')) {
            $hasOldColumn = Schema::hasColumn($t, 'kp_user_w_pref_id_fk');

            // เช็กก่อนเปลี่ยนโครงสร้าง: ทุกบิลต้องหา users.id ได้ ไม่อย่างนั้นหยุดโดยไม่แตะตาราง
            if ($hasOldColumn) {
                $unmapped = DB::table("{$t} as t")
                    ->leftJoin('kp_user_waste_preferences as p', 'p.id', '=', 't.kp_user_w_pref_id_fk')
                    ->whereNull('p.user_id')
                    ->count();
                if ($unmapped > 0) {
                    throw new RuntimeException("{$t}: {$unmapped} rows cannot map kp_user_w_pref_id_fk to users.id; nothing changed.");
                }
            }

            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('kp_u_trans_no');
            });

            if ($hasOldColumn) {
                DB::statement("
                    UPDATE {$t} t
                    JOIN kp_user_waste_preferences p ON p.id = t.kp_user_w_pref_id_fk
                    SET t.user_id = p.user_id
                ");

                $this->dropForeignKeys($t, 'kp_user_w_pref_id_fk');
                Schema::table($t, function (Blueprint $table) {
                    $table->dropColumn('kp_user_w_pref_id_fk');
                });
            }
        }

        if (DB::table($t)->whereNull('user_id')->doesntExist()) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        }
        $this->ensureForeignKey($t, 'user_id', 'users', 'kp_purchase_transactions_kp_user_w_pref_id_fk_foreign', 'CASCADE');

        // 2. ผู้บันทึก: เดิมอ้าง staffs ปัจจุบันโค้ดบันทึก users.id
        $this->dropForeignKeys($t, 'recorder_id', 'users');
        $this->ensureForeignKey($t, 'recorder_id', 'users', 'staff_fk');

        // 3. ตู้ kiosk: เดิมเป็น string default '0' -> bigint unsigned nullable
        if (Schema::hasColumn($t, 'kiosk_id_fk') && $this->columnType($t, 'kiosk_id_fk') !== 'bigint') {
            $this->dropForeignKeys($t, 'kiosk_id_fk');
            DB::statement("ALTER TABLE {$t} MODIFY kiosk_id_fk VARCHAR(191) NULL DEFAULT NULL");
            DB::statement("
                UPDATE {$t} SET kiosk_id_fk = NULL
                WHERE kiosk_id_fk IS NOT NULL
                  AND kiosk_id_fk NOT IN (SELECT CAST(id AS CHAR) FROM kiosks)
            ");
            DB::statement("ALTER TABLE {$t} MODIFY kiosk_id_fk BIGINT UNSIGNED NULL DEFAULT NULL");
        }
        $this->ensureForeignKey($t, 'kiosk_id_fk', 'kiosks', 'kp_purchase_transactions_kiosk_id_foreign');
    }

    private function syncSellTransactions(): void
    {
        $t = 'kp_sell_transactions';

        if (! Schema::hasColumn($t, 'org_id_fk')) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('org_id_fk')->nullable()->after('id');
                $table->index('org_id_fk', 'xx');
            });
            DB::statement("
                UPDATE {$t} s
                JOIN kp_purchase_shops sh ON sh.id = s.shop_id_fk
                SET s.org_id_fk = sh.org_id_fk
                WHERE s.org_id_fk IS NULL
            ");
        }
        if (DB::table($t)->whereNull('org_id_fk')->doesntExist()) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('org_id_fk')->nullable(false)->change();
            });
        } else {
            Log::warning("{$t}: some rows have no org_id_fk (no shop to map from); column left nullable.");
        }

        if (! Schema::hasColumn($t, 'user_id_fk')) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('user_id_fk')->nullable()->after('org_id_fk');
            });
            // เหมือน KpSellController: ผู้ขายคือเจ้าของบัญชีธนาคารขยะของหน่วยงาน
            DB::statement("
                UPDATE {$t} s
                JOIN kp_bank_accounts a ON a.org_id_fk = s.org_id_fk AND a.entity_type = 'org_recycle_bank'
                SET s.user_id_fk = a.user_id
                WHERE s.user_id_fk IS NULL
            ");
        }
        $this->ensureForeignKey($t, 'user_id_fk', 'users', 'user_id_fk');

        // ผู้บันทึก: โค้ดบันทึก users.id (validate กับ staffs.user_id) ใน DB ปัจจุบันไม่มี FK
        $this->dropForeignKeys($t, 'recorder_id');
    }

    private function syncSellDetails(): void
    {
        $t = 'kp_sell_details';

        if (! Schema::hasColumn($t, 'org_id_fk')) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('org_id_fk')->nullable()->after('id');
            });
            DB::statement("
                UPDATE {$t} d
                JOIN kp_sell_transactions s ON s.id = d.kp_sell_trans_id
                SET d.org_id_fk = s.org_id_fk
                WHERE d.org_id_fk IS NULL
            ");
        }
        if (DB::table($t)->whereNull('org_id_fk')->doesntExist()) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('org_id_fk')->nullable(false)->change();
            });
        } else {
            Log::warning("{$t}: some rows have no org_id_fk; column left nullable.");
        }
        $this->ensureForeignKey($t, 'org_id_fk', 'organizations', 'kp_sell_details_org_id_fk');
    }

    private function columnType(string $table, string $column): ?string
    {
        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->value('DATA_TYPE');
    }

    /**
     * ลบ FK ทั้งหมดของคอลัมน์ ยกเว้นที่อ้างอิงตาราง $keepReferenced
     */
    private function dropForeignKeys(string $table, string $column, ?string $keepReferenced = null): void
    {
        $fks = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->get(['CONSTRAINT_NAME', 'REFERENCED_TABLE_NAME']);

        foreach ($fks as $fk) {
            if ($keepReferenced !== null && $fk->REFERENCED_TABLE_NAME === $keepReferenced) {
                continue;
            }
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
    }

    /**
     * เพิ่ม FK ถ้ายังไม่มี และข้อมูลไม่มีค่าที่อ้างอิงไม่ได้ (ถ้ามีจะข้ามและบันทึก log)
     */
    private function ensureForeignKey(string $table, string $column, string $references, string $name, ?string $onDelete = null): void
    {
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->where('REFERENCED_TABLE_NAME', $references)
            ->exists();
        if ($exists) {
            return;
        }

        $orphans = DB::table($table)
            ->whereNotNull($column)
            ->whereNotIn($column, DB::table($references)->select('id'))
            ->count();
        if ($orphans > 0) {
            Log::warning("{$table}.{$column}: {$orphans} rows reference missing {$references}.id; foreign key not added.");
            return;
        }

        $nameTaken = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::raw('DATABASE()'))
            ->where('CONSTRAINT_NAME', $name)
            ->exists();
        $name = $nameTaken ? "{$table}_{$column}_foreign" : $name;

        $sql = "ALTER TABLE {$table} ADD CONSTRAINT `{$name}` FOREIGN KEY (`{$column}`) REFERENCES `{$references}` (`id`)";
        if ($onDelete !== null) {
            $sql .= " ON DELETE {$onDelete}";
        }
        DB::statement($sql);
    }
};
