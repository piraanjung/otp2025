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
        Schema::create('cutmeter', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('meter_id_fk');
            $table->integer('owe_count');
            $table->string('progress')->comment('เก็บเป็น json มี status, datetime, twman_id');
            $table->enum('status', ['init', 'cutmeter', 'install', 'complete', 'cancel']);
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('cutmeter');
    }
};
