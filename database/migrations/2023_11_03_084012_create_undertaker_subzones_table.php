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
        Schema::create('undertaker_subzones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('twman_id')->constrained('staffs')->onDelete('cascade');
            $table->foreignId('subzone_id')->constrained('subzones')->onDelete('cascade');
;
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
        Schema::dropIfExists('undertaker_subzones');
    }
};
