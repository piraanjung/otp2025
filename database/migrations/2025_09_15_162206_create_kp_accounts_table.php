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
        Schema::create('kp_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('u_wpref_id_fk')->primary();
            $table->foreign('u_wpref_id_fk')->references('id')->on('kp_user_waste_preferences')->onDelete('cascade');
            $table->float('balance',8,2)->default(0);
            $table->float('points',8,2)->default(0);
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
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
        Schema::dropIfExists('kp_accounts');
    }
};
