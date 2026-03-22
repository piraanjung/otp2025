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
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            // ทำ Foreign Key Constraint
            $table->dropColumn('unit_bank');
            $table->dropColumn('unit_kiosk');
            $table->unsignedBigInteger('unit_bank_idfk')->nullable()->after('kp_itemsname');
            $table->unsignedBigInteger('unit_kiosk_idfk')->nullable()->after('unit_bank_idfk');
            $table->foreign('unit_bank_idfk')->references('id')->on('kp_tbank_items_units');
            $table->foreign('unit_kiosk_idfk')->references('id')->on('kp_tbank_items_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            $table->dropForeign(['unit_bank_idfk', 'unit_kiosk_idfk']);
            $table->dropColumn('unit_bank_idfk');
            $table->dropColumn('unit_kiosk_idfk');
        });
    }
};
