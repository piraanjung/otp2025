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
        Schema::table('tw_meter_type_rate_configs', function (Blueprint $table) {
            $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tw_meter_type_rate_configs', function (Blueprint $table) {
            $table->dropForeign('org_id_fk');
            $table->dropColumn('org_id_fk');
        });
    }
};
