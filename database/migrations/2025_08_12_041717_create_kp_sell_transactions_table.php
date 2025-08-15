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
                Schema::create('kp_sell_transactions', function (Blueprint $table) {
                    $table->id();
                    $table->string('kp_u_trans_no')->unique(); // เลขที่ธุรกรรมการขาย
                    $table->foreignId('shop_id_fk')->nullable()->constrained('kp_purchase_shops')->onDelete('set null');; // ชื่อร้านรับซื้อ
                    $table->date('transaction_date');
                    $table->decimal('total_weight', 10, 2)->default(0.00); // น้ำหนักรวม
                    $table->decimal('total_amount', 10, 2)->default(0.00); // ยอดรวมเป็นเงิน
                    $table->string('status')->default('completed'); // สถานะธุรกรรม (e.g., completed, cancelled)
                    $table->foreignId('recorder_id')->nullable()->constrained('staffs','user_id')->onDelete('set null'); // ผู้บันทึก (พนักงาน/ผู้ดูแล)
                    $table->timestamps();
                });
            }

            /**
             * Reverse the migrations.
             */
            public function down(): void
            {
                Schema::dropIfExists('kp_sell_transactions');
            }
        };
        