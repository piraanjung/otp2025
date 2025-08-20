<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tw_pricing_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // เช่น 'Fixed Rate', 'Progressive Rate'
            $table->text('description')->nullable();
            $table->timestamps();
        });

        //Insert initial data
        // Schema::table('pricing_types', function (Blueprint $table) {
        //     DB::table('pricing_types')->insert([
        //         ['name' => 'Fixed Rate', 'description' => 'อัตราคงที่ หรือ มีค่าบำรุงรักษามิเตอร์'],
        //         ['name' => 'Progressive Rate', 'description' => 'อัตราก้าวหน้า ตามช่วงการใช้น้ำ'],
        //     ]);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tw_pricing_types');
    }
};
