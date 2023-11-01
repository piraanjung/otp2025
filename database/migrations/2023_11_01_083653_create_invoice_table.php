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
        Schema::create('invoice', function (Blueprint $table) {
                $table->unsignedBigInteger('meter_id_fk');
                $table->unsignedBigInteger('inv_period_id_fk');
                $table->float('lastmeter');
                $table->float('currentmeter');
                $table->enum('status', ['init','invoice', 'paid', 'owe']);
                $table->string('receipt_id')->default(0);
                $table->string('comment')->nullable();
                $table->integer('recorder_id');
                $table->timestamps();
                $table->primary(['meter_id_fk','inv_period_id_fk']);
                $table->foreign('meter_id_fk')->references('meter_id')->on('user_meter_infos')->onDelete('cascade');
                $table->foreign('inv_period_id_fk')->references('id')->on('invoice_period')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice');
    }
};
