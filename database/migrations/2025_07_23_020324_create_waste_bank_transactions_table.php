<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWasteBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waste_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // สมาชิกที่นำขยะมาขาย
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null'); // เจ้าหน้าที่ที่ทำรายการ (FK to users.id)

            $table->decimal('total_member_payout_amount', 10, 2)->default(0.00); // ยอดรวมที่จ่ายให้สมาชิก
            $table->decimal('estimated_factory_revenue', 10, 2)->default(0.00); // ยอดรวมที่คาดว่าจะได้รับจากโรงงาน

            $table->date('transaction_date'); // วันที่ทำรายการ
            $table->string('receipt_code')->unique()->nullable(); // รหัสใบเสร็จ/การทำรายการ
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed'); // สถานะธุรกรรม
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
        Schema::dropIfExists('waste_bank_transactions');
    }
}
