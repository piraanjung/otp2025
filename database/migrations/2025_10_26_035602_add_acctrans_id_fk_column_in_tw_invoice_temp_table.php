<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tw_invoice_temp', function (Blueprint $table) {
            $table->foreignId('acc_trans_id_fk')->constrained('tw_acc_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tw_invoice_temp', function (Blueprint $table) {
            $table->dropForeign('acc_trans_id_fk');
            $table->dropColumn('acc_trans_id_fk');
        });
    }
};
