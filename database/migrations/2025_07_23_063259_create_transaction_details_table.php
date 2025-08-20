<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_bank_transaction_id')->constrained('waste_bank_transactions')->onDelete('cascade');
            $table->foreignId('waste_type_id')->constrained('waste_types')->onDelete('cascade');

            $table->decimal('quantity', 10, 2); // จำนวน (น้ำหนัก หรือ ชิ้น)
            $table->string('unit_used'); // หน่วยที่ใช้จริง ('kg' หรือ 'piece')

            $table->decimal('member_price_per_unit', 10, 2); // ราคาที่คุณซื้อจากสมาชิก (ต่อหน่วยที่ใช้)
            $table->decimal('total_member_amount', 10, 2); // ยอดรวมที่จ่ายให้สมาชิกสำหรับรายการนี้

            $table->decimal('factory_price_per_unit', 10, 2); // ราคาที่โรงงานซื้อจากคุณ (ต่อหน่วยที่ใช้)
            $table->decimal('total_factory_amount', 10, 2); // ยอดรวมที่คาดว่าจะได้รับจากโรงงานสำหรับรายการนี้
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
        Schema::dropIfExists('transaction_details');
    }
}
