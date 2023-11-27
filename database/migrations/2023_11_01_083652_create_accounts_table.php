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
        Schema::create('accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('id');
                $table->float('deposit',6,2)->comment('ยอดฝาก');
                $table->unsignedBigInteger('payee');
                $table->text('comment')->nullable();
                $table->timestamps();
                $table->primary('id');
                $table->foreign('payee')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
