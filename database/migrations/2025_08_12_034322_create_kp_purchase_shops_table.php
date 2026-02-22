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
            Schema::create('kp_purchase_shops', function (Blueprint $table) {
                $table->id();
                $table->string('shop_name')->unique(); // ชื่อร้านรับซื้อ
                $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
                $table->string('contact_person')->nullable(); // ชื่อผู้ติดต่อ
                $table->string('phone')->nullable(); // เบอร์โทรศัพท์
                $table->text('address')->nullable(); // ที่อยู่
                $table->enum('status', ['active', 'inactive'])->default('active'); // สถานะการใช้งาน
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('kp_purchase_shops');
        }
    };
    