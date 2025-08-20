<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualCollectionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annual_collection_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('number_of_bins')->default(0); // จำนวนถังที่มี
            $table->decimal('amount_due', 10, 2)->default(0.00); // ยอดที่ต้องชำระ
            $table->decimal('amount_paid', 10, 2)->default(0.00); // ยอดที่ชำระแล้ว
            $table->date('due_date'); // วันที่ครบกำหนดชำระ
            $table->date('payment_date')->nullable(); // วันที่ชำระจริง
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('annual_collection_payments');
    }
}