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
        Schema::create('food_waste_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
            $table->decimal('points_balance', 12, 2)->default(0);
            $table->decimal('money_balance', 12, 2)->default(0);
            $table->float('total_weight_kg')->default(0); // น้ำหนักสะสมรวม
            $table->dateTime('last_contributed_at'); // น้ำหนักสะสมรวม
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_waste_accounts');
    }
};
