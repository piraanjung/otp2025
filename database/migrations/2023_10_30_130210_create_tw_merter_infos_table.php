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
        Schema::create('tw_meter_infos', function (Blueprint $table) {
            $table->unsignedBigInteger('meter_id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('meternumber');
            $table->string('factory_no');
            $table->string('submeter_name')->nullable();
            $table->string('meter_address');
            $table->unsignedBigInteger('undertake_zone_id');
            $table->unsignedBigInteger('undertake_subzone_id');
            $table->date('acceptance_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('comment')->nullable();
            $table->integer('owe_count')->default(0);
            $table->enum('cutmeter', [1, 0])->default(0);
            $table->integer('inv_no_index')->default(0);
            $table->integer('payment_id')->default(0);
            $table->integer('discounttype')->default(0);
            $table->integer('recorder_id');
            $table->integer('last_meter_recording')->default(0);
            $table->timestamps();
            $table->foreign('undertake_zone_id')->references('id')->on('zones');
            $table->foreign('undertake_subzone_id')->references('id')->on('subzones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tw_meter_infos');
    }
};
