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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('prefix')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('line_id')->nullable();
            $table->string('id_card')->nullable();
            $table->string('phone')->nullable();
            $table->text('image')->nullable();
            $table->enum('gender',['m', 'w'])->nullable();
            $table->string('address')->nullable();
            $table->timestamp('email_verified_at')->nullable()->nullable();
            $table->rememberToken()->nullable();
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active')->nullable();
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
        Schema::dropIfExists('users');
    }
};
