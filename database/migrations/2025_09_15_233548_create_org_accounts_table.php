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
        Schema::create('org_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('acc_number');
            $table->float('tabwater_balance', 8,2)->default(0);
            $table->float('annaul_balance', 8,2)->default(0);
            $table->float('recycle_bank_balance', 8,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('org_accounts');
    }
};
