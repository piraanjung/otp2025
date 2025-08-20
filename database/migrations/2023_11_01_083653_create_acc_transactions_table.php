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
        Schema::create('acc_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id_fk');
            $table->float('paidsum', 8,2);
            $table->float('vatsum', 8,2);
            $table->float('reserve_meter_sum', 8,2);
            $table->float('totalpaidsum', 8,2);
            $table->enum('status', ['1','0'])->comment('1= active, 0=deleted');
            $table->unsignedBigInteger('cashier')->comment('ผู้รับเงิน');
            $table->timestamps();

            $table->foreign('user_id_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cashier')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_transactions');
    }
};
