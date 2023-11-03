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
                $table->float('lastmeter', 6,2);
                $table->float('currentmeter', 6,2);
                $table->enum('status', ['init','invoice', 'paid', 'owe']);
                $table->unsignedBigInteger('accounts_id_fk')->default(0);
                $table->string('comment')->nullable();
                $table->unsignedBigInteger('recorder_id');
                $table->timestamps();
                $table->primary(['meter_id_fk','inv_period_id_fk']);
                $table->foreign('meter_id_fk')->references('meter_id')->on('user_meter_infos')->onDelete('cascade');
                $table->foreign('inv_period_id_fk')->references('id')->on('invoice_period')->onDelete('cascade');
                $table->foreign('accounts_id_fk')->references('id')->on('accounts')->onDelete('cascade');
                $table->foreign('recorder_id')->references('id')->on('users')->onDelete('cascade');

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
