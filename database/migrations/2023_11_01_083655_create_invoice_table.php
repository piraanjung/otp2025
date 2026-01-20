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
        Schema::create('tw_invoice', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('meter_id_fk');
                $table->unsignedBigInteger('inv_period_id_fk');
                $table->float('lastmeter', 8,2);
                $table->float('currentmeter', 8,2);
                $table->float('water_used', 8,2);
                $table->enum('inv_type', ['r', 'u'])->comment('r =รักษามิเตอร์, u=ใช้น้ำ');
                $table->float('paid', 8,2);
                $table->float('reserve_meter', 6,2);
                $table->float('vat', 8,2);
                $table->float('totalpaid', 8,2)->commet('paid+vat');
                $table->enum('status', ['init','invoice', 'paid', 'owe', 'deleted']);
                $table->unsignedBigInteger('acc_trans_id_fk')->nullable();
                $table->string('comment')->nullable();
                $table->unsignedBigInteger('recorder_id');
                $table->timestamps();
                $table->foreign('meter_id_fk')->references('meter_id')->on('tw_meter_infos')->onDelete('cascade');
                $table->foreign('inv_period_id_fk')->references('id')->on('invoice_period')->onDelete('cascade');
                $table->foreign('acc_trans_id_fk')->references('id')->on('tw_acc_transactions')->onDelete('cascade');
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
        Schema::dropIfExists('tw_invoice');
    }
};
