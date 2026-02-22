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
        Schema::create('tw_cutmeter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id_fk')->constrained('tw_meter_infos')->onDelete('cascade');
            $table->integer('owe_count')->default(0);
            $table->integer('warning_print')->default(0);
            $table->enum('status', ['pending', 'cutmeter', 'install', 'complete', 'cancel']);
            $table->string('operate_by')->comment('รหัส staffs ถ้า >1 ใช้ | แยก');
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
        Schema::dropIfExists('tw_cutmeter');
    }
};
