<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kp_tbank_items_groups', function (Blueprint $table) {
            $table->id();
            $table->string('kp_items_groupname');
            $table->enum('status', ['active','inactive'])->default('active');
            $table->enum('deleted', [0,1])->default('0');
            $table->timestamps();

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_tbank_items_groups');
    }
};
