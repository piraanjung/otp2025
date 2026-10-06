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
        Schema::create('kp_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_no');
            $table->unsignedBigInteger('u_wpref_id_fk')->primary();
            $table->foreign('org_id_fk')->references('id')->on('organizations')->onDelete('cascade');
            $table->enum('entity_type',['recycle_bank', 'foodwaste_bank', 'org_recycle']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
