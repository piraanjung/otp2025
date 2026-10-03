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
       Schema::table('inv_item_details', function (Blueprint $table) {
        $table->unsignedBigInteger('location_id_fk')->nullable()->after('expire_date');
        $table->foreign('location_id_fk')->references('id')->on('inv_locations')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_item_details', function (Blueprint $table) {
            //
        });
    }
};
