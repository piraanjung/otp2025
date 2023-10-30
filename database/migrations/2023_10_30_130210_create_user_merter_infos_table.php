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
        Schema::create('user_meter_infos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('meternumber');
            $table->unsignedBigInteger('metertype_id');
            $table->unsignedBigInteger('undertake_zone_id');
            $table->unsignedBigInteger('undertake_subzone_id');
            $table->date('acceptace_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('comment')->nullable();
            $table->integer('owe_count')->default(0);
            $table->integer('payment_id')->default(0);
            $table->integer('discounttype')->default(0);
            $table->integer('recorder_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('metertype_id')->references('id')->on('meter_types');
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
        Schema::dropIfExists('user_merter_infos');
    }
};
