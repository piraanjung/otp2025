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
       Schema::create('invoice_temp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('meter_id_fk');
                $table->unsignedBigInteger('inv_period_id_fk');
                $table->float('lastmeter', 8,2);
                $table->float('currentmeter', 8,2);
                $table->float('reserve_merer', 6,2)->default(0);
                $table->float('water_used', 8,2);
                $table->float('paid', 8,2);
                $table->float('vat', 8,2);
                $table->float('totalpaid', 8,2)->commet('paid+vat');
                $table->enum('status', ['init','invoice', 'paid', 'deleted']);
                $table->string('comment')->nullable();
                $table->string('inv_grouped')->nullable();
                $table->integer('printed_time')->default(0);
                $table->unsignedBigInteger('recorder_id');
                $table->timestamps();
                $table->foreign('meter_id_fk')->references('meter_id')->on('tw_meter_infos')->onDelete('cascade');
                $table->foreign('inv_period_id_fk')->references('id')->on('invoice_period')->onDelete('cascade');
                $table->foreign('recorder_id')->references('id')->on('staffs')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_temp');
    }
};
