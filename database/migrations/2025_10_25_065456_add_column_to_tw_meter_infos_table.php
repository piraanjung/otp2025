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
        Schema::table('tw_meter_infos', function (Blueprint $table) {
            $table->foreignId('metertype_id')->constrained('tw_meter_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tw_meter_infos', function (Blueprint $table) {
            $table->dropForeign(['metertype_id']);
            $table->dropColumn('metertype_id');
        });
    }
};
