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
        Schema::create('invoice_period', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('inv_p_name');
            $table->unsignedBigInteger('budgetyear_id');
            $table->date('startdate');
            $table->date('enddate');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
            $table->foreign('budgetyear_id')->references('id')->on('budget_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_period');
    }
};
