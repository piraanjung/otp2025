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
        Schema::create('kp_usergroups', function (Blueprint $table) {
            $table->id();
            $table->string('usergroup_name');
            $table->enum('status', ['active', 'invactive']);
            $table->enum('deleted',['0', '1'])->default('0');
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
        Schema::dropIfExists('kp_usergroups');
    }
};
