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
        Schema::create('kp_tbank_items_pricepoint', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kp_items_idfk');
            $table->float('price_from_dealer', 8,2);
            $table->float('price_for_member', 8,2);
            $table->float('point');
            $table->enum('type',['tbank','tbox']);
            $table->unsignedBigInteger('kp_units_idfk');
            $table->date('effective_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('deleted', [0,1])->default('0');
            $table->unsignedBigInteger('recorder_id');
            $table->timestamps();

            $table->foreign('kp_items_idfk')->references('id')->on('kp_tbank_items')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kp_units_idfk')->references('id')->on('kp_tbank_items_units')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('recorder_id')->references('id')->on('staffs')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_tbank_items_pricepoint');
    }
};
