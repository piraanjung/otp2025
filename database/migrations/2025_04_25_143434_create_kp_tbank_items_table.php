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
        Schema::create('kp_tbank_items', function (Blueprint $table) {
            $table->id();
            $table->string('kp_itemscode');
            $table->string('kp_itemsname');
            $table->unsignedBigInteger('kp_items_group_idfk');
            $table->integer('favorite')->default(0);
            $table->enum('status', ['active', 'inactive']);
            $table->string('image')->nullable();
            $table->enum('deleted', [0,1])->default('0');
            $table->timestamps();

            $table->foreign('kp_items_group_idfk')->references('id')->on('kp_tbank_items_groups')
                ->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_tbank_items');
    }
};
