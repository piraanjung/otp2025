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
                Schema::create('kp_sell_details', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('kp_sell_trans_id')->constrained('kp_sell_transactions')->onDelete('cascade');
                    $table->foreignId('kp_recycle_item_id')->constrained('kp_tbank_items')->onDelete('restrict');
                    $table->decimal('weight', 10, 2); // น้ำหนักของขยะประเภทนี้
                    $table->decimal('price_per_unit', 8, 2); // ราคาต่อหน่วยที่ขายได้
                    $table->decimal('amount', 10, 2); // ยอดเงินสำหรับรายการนี้
                    $table->text('comment')->nullable();
                    $table->timestamps();
                });
            }

            /**
             * Reverse the migrations.
             */
            public function down(): void
            {
                Schema::dropIfExists('kp_sell_details');
            }
        };
        