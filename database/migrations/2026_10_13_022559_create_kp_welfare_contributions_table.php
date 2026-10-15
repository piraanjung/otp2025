<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payout_id')->nullable()->constrained('welfare_payouts')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(60.00);
            $table->enum('payment_method', ['auto_wallet', 'cash', 'transfer', 'pending_deduct'])->default('pending_deduct');
            $table->enum('status', ['pending', 'paid', 'exempted'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('slip_img')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_contributions');
    }
};